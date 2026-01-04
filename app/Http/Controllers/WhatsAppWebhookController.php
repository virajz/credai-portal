<?php

namespace App\Http\Controllers;

use App\Http\Requests\WhatsAppWebhookRequest;
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

        return response()->json([
            'success' => true,
            'message' => 'Webhook received successfully',
        ], 200);
    }
}
