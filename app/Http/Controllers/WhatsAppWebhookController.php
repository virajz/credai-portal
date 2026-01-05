<?php

namespace App\Http\Controllers;

use App\Http\Requests\WhatsAppWebhookRequest;
use App\Jobs\SendCompanyDetailsMessage;
use App\Models\Company;
use App\Models\WhatsAppMessage;
use App\Services\WhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    public function __construct(
        protected WhatsAppService $whatsappService
    ) {}

    /**
     * Handle incoming WhatsApp webhook
     */
    public function handle(WhatsAppWebhookRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Log the incoming message for debugging
        Log::info('WhatsApp message received', [
            'message_id' => $data['messageId'],
            'from' => $data['from'],
            'sender_name' => $data['whatsapp']['senderName'],
            'content_type' => $data['content']['contentType'],
            'text' => $data['content']['text'] ?? null,
        ]);

        // Check if this is the first message from this sender
        $isFirstMessage = WhatsAppMessage::isFirstMessageFrom($data['from']);

        // Store the message in database
        $message = WhatsAppMessage::create([
            'message_id' => $data['messageId'],
            'channel' => $data['channel'],
            'from' => $data['from'],
            'to' => $data['to'],
            'sender_name' => $data['whatsapp']['senderName'],
            'content_type' => $data['content']['contentType'],
            'text' => $data['content']['text'] ?? null,
            'raw_payload' => $data,
            'received_at' => $data['receivedAt'],
            'thank_you_sent' => false,
        ]);

        // Send thank you message only for first-time users
        if ($isFirstMessage) {
            $thankYouMessage = 'Thank you, we will get back to you.';

            $result = $this->whatsappService->sendSessionMessage(
                $data['from'],
                $thankYouMessage
            );

            if ($result['success']) {
                $message->update(['thank_you_sent' => true]);

                Log::info('Thank you message sent', [
                    'message_id' => $data['messageId'],
                    'from' => $data['from'],
                    'sender_name' => $data['whatsapp']['senderName'],
                ]);
            } else {
                Log::error('Failed to send thank you message', [
                    'message_id' => $data['messageId'],
                    'from' => $data['from'],
                    'error' => $result['error'] ?? 'Unknown error',
                ]);
            }
        }

        // Check if message contains company inquiry (format: "Hi, I want to know more about CompanyName - uuid123")
        $messageText = $data['content']['text'] ?? '';
        if ($this->isCompanyInquiry($messageText)) {
            $this->handleCompanyInquiry($messageText, $data['from']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Webhook received successfully',
        ], 200);
    }

    /**
     * Check if the message is a company inquiry
     */
    protected function isCompanyInquiry(string $message): bool
    {
        return preg_match('/Hi,\s+I\s+want\s+to\s+know\s+more\s+about\s+.+\s+-\s+[a-f0-9]{6}/i', $message) === 1;
    }

    /**
     * Handle company inquiry and send company details
     */
    protected function handleCompanyInquiry(string $message, string $phoneNumber): void
    {
        // Extract company name and UUID last 6 from message
        // Format: "Hi, I want to know more about Atlanta - ffb590"
        if (! preg_match('/Hi,\s+I\s+want\s+to\s+know\s+more\s+about\s+(.+?)\s+-\s+([a-f0-9]{6})/i', $message, $matches)) {
            return;
        }

        $companyName = trim($matches[1]);
        $uuidLast6 = strtolower($matches[2]);

        // Find the company by name and UUID last 6 characters
        $company = Company::where('company_name', 'ILIKE', $companyName)
            ->get()
            ->first(function ($company) use ($uuidLast6) {
                return strtolower(substr(str_replace('-', '', $company->uuid), -6)) === $uuidLast6;
            });

        if (! $company) {
            Log::warning('Company not found for inquiry', [
                'company_name' => $companyName,
                'uuid_last_6' => $uuidLast6,
                'phone_number' => $phoneNumber,
            ]);

            return;
        }

        // Dispatch job to send company details
        SendCompanyDetailsMessage::dispatch($company->id, $phoneNumber);

        Log::info('Company details job dispatched', [
            'company_id' => $company->id,
            'company_name' => $company->company_name,
            'phone_number' => $phoneNumber,
        ]);
    }
}
