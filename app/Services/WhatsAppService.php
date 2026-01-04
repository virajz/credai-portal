<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    public function __construct(
        protected string $apiUrl = '',
        protected string $sessionMessageUrl = '',
        protected string $authToken = '',
        protected string $originWebsite = ''
    ) {
        $this->apiUrl = config('whatsapp.api_url');
        $this->sessionMessageUrl = config('whatsapp.session_message_url');
        $this->authToken = config('whatsapp.auth_token');
        $this->originWebsite = config('whatsapp.origin_website');
    }

    /**
     * Send a template message via WhatsApp.
     */
    public function sendTemplate(
        string $name,
        string $phoneNumber,
        string $templateName,
        array $data = [],
        ?string $imageUrl = null,
        ?string $buttonValue = null,
        string $language = 'en'
    ): array {
        $payload = [
            'authToken' => $this->authToken,
            'name' => $name,
            'sendto' => $phoneNumber,
            'originWebsite' => $this->originWebsite,
            'templateName' => $templateName,
            'language' => $language,
            'data' => $data,
        ];

        if ($imageUrl) {
            $payload['myfile'] = $imageUrl;
        }

        if ($buttonValue) {
            $payload['buttonValue'] = $buttonValue;
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($this->apiUrl, $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'body' => $response->body(),
                ];
            }

            return [
                'success' => false,
                'error' => 'HTTP '.$response->status().': '.$response->body(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send a session message via WhatsApp.
     */
    public function sendSessionMessage(
        string $phoneNumber,
        string $text
    ): array {
        $payload = [
            'sendto' => $phoneNumber,
            'authToken' => $this->authToken,
            'originWebsite' => $this->originWebsite,
            'contentType' => 'text',
            'text' => $text,
        ];

        try {
            $response = Http::timeout(30)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($this->sessionMessageUrl.'/sendMessages', $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'body' => $response->body(),
                ];
            }

            return [
                'success' => false,
                'error' => 'HTTP '.$response->status().': '.$response->body(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
