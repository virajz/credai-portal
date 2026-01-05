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

        // Check if message contains company inquiry (format: "Hi, I want to know more about CompanyName - uuid123")
        $messageText = $data['content']['text'] ?? '';
        if (! $this->isCompanyInquiry($messageText)) {
            // Ignore all other messages - only process company inquiries
            Log::info('Message ignored - not a company inquiry', [
                'message_id' => $data['messageId'],
                'from' => $data['from'],
                'text' => $messageText,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Webhook received successfully',
            ], 200);
        }

        // Store the message in database
        WhatsAppMessage::create([
            'message_id' => $data['messageId'],
            'channel' => $data['channel'],
            'from' => $data['from'],
            'to' => $data['to'],
            'sender_name' => $data['whatsapp']['senderName'],
            'content_type' => $data['content']['contentType'],
            'text' => $data['content']['text'] ?? null,
            'raw_payload' => $data,
            'received_at' => $data['receivedAt'],
        ]);

        // Handle company inquiry
        $this->handleCompanyInquiry($messageText, $data['from'], $data['whatsapp']['senderName']);

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
    protected function handleCompanyInquiry(string $message, string $phoneNumber, string $senderName): void
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

            // Send company not found message
            $notFoundMessage = "Hello {$senderName}, we could not find the company you are looking for. Please check the company name and try again.";
            $result = $this->whatsappService->sendSessionMessage($phoneNumber, $notFoundMessage);

            if ($result['success']) {
                Log::info('Company not found message sent', [
                    'phone_number' => $phoneNumber,
                    'company_name' => $companyName,
                ]);
            } else {
                Log::error('Failed to send company not found message', [
                    'phone_number' => $phoneNumber,
                    'error' => $result['error'] ?? 'Unknown error',
                ]);
            }

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
