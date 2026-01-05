<?php

namespace App\Jobs;

use App\Models\Company;
use App\Services\WhatsAppService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendCompanyBrochure implements ShouldQueue
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
            Log::error('Company not found for brochure', [
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

        // Check if brochure exists
        if (! $company->exhibitor->brochure_path) {
            Log::info('No brochure available for company', [
                'company_id' => $company->id,
                'company_name' => $company->company_name,
            ]);

            return;
        }

        Log::info('Attempting to send company brochure', [
            'company_id' => $company->id,
            'company_name' => $company->company_name,
            'phone_number' => $this->phoneNumber,
        ]);

        // Get brochure URL
        $brochureUrl = url(\Storage::url($company->exhibitor->brochure_path));
        $brochureMessage = "Here is the company profile for *{$company->company_name}*.";

        // Send the brochure
        $result = $whatsappService->sendSessionMessage(
            $this->phoneNumber,
            $brochureMessage,
            $brochureUrl
        );

        if (! $result['success']) {
            Log::error('Failed to send company brochure', [
                'company_id' => $company->id,
                'error' => $result['error'],
            ]);

            throw new \Exception('WhatsApp API error: '.$result['error']);
        }

        Log::info('Company brochure sent successfully', [
            'company_id' => $company->id,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::critical('Company brochure job failed after all retries', [
            'company_id' => $this->companyId,
            'phone_number' => $this->phoneNumber,
            'exception' => $exception->getMessage(),
        ]);
    }
}
