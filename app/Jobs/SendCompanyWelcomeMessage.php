<?php

namespace App\Jobs;

use App\Models\Company;
use App\Services\WhatsAppService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendCompanyWelcomeMessage implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $maxExceptions = 2;

    public int $timeout = 30;

    public function __construct(
        public int $companyId,
        public string $phoneNumber
    ) {}

    public function handle(WhatsAppService $whatsappService): void
    {
        $company = Company::with(['exhibitor'])->find($this->companyId);

        if (! $company) {
            Log::error('Company not found for welcome message', [
                'company_id' => $this->companyId,
                'phone_number' => $this->phoneNumber,
            ]);

            return;
        }

        if (! $company->exhibitor) {
            Log::error('Exhibitor not found for company', [
                'company_id' => $company->id,
                'company_name' => $company->company_name,
            ]);

            return;
        }

        Log::info('Attempting to send welcome message', [
            'company_id' => $company->id,
            'company_name' => $company->company_name,
            'phone_number' => $this->phoneNumber,
        ]);

        // Build the welcome message
        $message = $this->buildWelcomeMessage($company);

        // Send the message
        $result = $whatsappService->sendSessionMessage($this->phoneNumber, $message);

        if (! $result['success']) {
            Log::error('Failed to send welcome message', [
                'company_id' => $company->id,
                'error' => $result['error'],
            ]);

            throw new \Exception('WhatsApp API error: '.$result['error']);
        }

        Log::info('Welcome message sent successfully', [
            'company_id' => $company->id,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::critical('Welcome message job failed after all retries', [
            'company_id' => $this->companyId,
            'phone_number' => $this->phoneNumber,
            'exception' => $exception->getMessage(),
        ]);
    }

    protected function buildWelcomeMessage(Company $company): string
    {
        $message = [];

        // Greeting
        $message[] = "Thank you for visiting *{$company->company_name}* at CREDAI GLAM Property Show.";
        $message[] = '';

        // Exhibitor link
        $exhibitorUrl = route('exhibitor.show', ['exhibitor' => $company->exhibitor->slug]);
        $message[] = 'Here is a link to know more about the company and projects:';
        $message[] = $exhibitorUrl;
        $message[] = '';

        // Brochure line or explore more
        if ($company->exhibitor->brochure_path) {
            $message[] = 'We are also sharing our company profile with you. We hope you find it insightful and look forward to connecting with you soon.';
        } else {
            $message[] = 'Feel free to explore more.';
        }

        return implode("\n", $message);
    }
}
