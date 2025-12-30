<?php

namespace App\Jobs;

use App\Services\WhatsAppService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendWhatsAppMessage implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $maxExceptions = 2;

    public int $timeout = 30;

    public function __construct(
        public string $name,
        public string $phoneNumber,
        public string $templateName,
        public array $data = [],
        public ?string $imageUrl = null,
        public ?string $buttonValue = null,
        public string $language = 'en',
        public ?int $visitorId = null
    ) {}

    public function handle(WhatsAppService $whatsappService): void
    {
        Log::channel('whatsapp')->info('Attempting to send WhatsApp message', [
            'visitor_id' => $this->visitorId,
            'phone' => $this->phoneNumber,
            'template' => $this->templateName,
            'image_url' => $this->imageUrl,
            'button_url' => $this->buttonValue,
        ]);

        $result = $whatsappService->sendTemplate(
            name: $this->name,
            phoneNumber: $this->phoneNumber,
            templateName: $this->templateName,
            data: $this->data,
            imageUrl: $this->imageUrl,
            buttonValue: $this->buttonValue,
            language: $this->language
        );

        if ($result['success']) {
            Log::channel('whatsapp')->info('WhatsApp message sent successfully', [
                'visitor_id' => $this->visitorId,
                'phone' => $this->phoneNumber,
                'template' => $this->templateName,
                'response' => $result['data'] ?? $result['body'],
            ]);
        } else {
            Log::channel('whatsapp')->error('WhatsApp message failed', [
                'visitor_id' => $this->visitorId,
                'phone' => $this->phoneNumber,
                'template' => $this->templateName,
                'image_url' => $this->imageUrl,
                'error' => $result['error'],
            ]);

            throw new \Exception('WhatsApp API error: '.$result['error']);
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::channel('whatsapp')->critical('WhatsApp job failed after all retries', [
            'visitor_id' => $this->visitorId,
            'phone' => $this->phoneNumber,
            'template' => $this->templateName,
            'exception' => $exception->getMessage(),
        ]);
    }
}
