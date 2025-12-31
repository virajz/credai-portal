<?php

namespace App\Jobs;

use App\Services\WhatsAppService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendOtpMessage implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $maxExceptions = 2;

    public int $timeout = 30;

    public function __construct(
        public string $name,
        public string $phoneNumber,
        public string $otp,
        public string $language = 'en'
    ) {}

    public function handle(WhatsAppService $whatsappService): void
    {
        $templateName = env('WABA_OTP_TEMPLATE_NAME', 'exhibitor_otp');

        Log::channel('whatsapp')->info('Attempting to send OTP via WhatsApp', [
            'phone' => $this->phoneNumber,
            'template' => $templateName,
        ]);

        $result = $whatsappService->sendTemplate(
            name: $this->name,
            phoneNumber: $this->phoneNumber,
            templateName: $templateName,
            data: [$this->otp],
            language: $this->language
        );

        if ($result['success']) {
            Log::channel('whatsapp')->info('OTP sent successfully via WhatsApp', [
                'phone' => $this->phoneNumber,
                'template' => $templateName,
                'response' => $result['data'] ?? $result['body'],
            ]);
        } else {
            Log::channel('whatsapp')->error('OTP WhatsApp message failed', [
                'phone' => $this->phoneNumber,
                'template' => $templateName,
                'error' => $result['error'],
            ]);

            throw new \Exception('WhatsApp API error: '.$result['error']);
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::channel('whatsapp')->critical('OTP WhatsApp job failed after all retries', [
            'phone' => $this->phoneNumber,
            'template' => env('WABA_OTP_TEMPLATE_NAME', 'exhibitor_otp'),
            'exception' => $exception->getMessage(),
        ]);
    }
}
