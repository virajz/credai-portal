<?php

namespace App\Jobs;

use App\Models\Company;
use App\Services\WhatsAppService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

class SendCompanyDetailsMessage implements ShouldQueue
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
        $company = Company::with(['exhibitor', 'exhibitor.projects'])->find($this->companyId);

        if (! $company) {
            Log::error('Company not found for sending details', [
                'company_id' => $this->companyId,
                'phone_number' => $this->phoneNumber,
            ]);

            return;
        }

        Log::info('Attempting to send company details', [
            'company_id' => $company->id,
            'company_name' => $company->company_name,
            'phone_number' => $this->phoneNumber,
        ]);

        // Step 1: Send company details (text only)
        $companyDetailsMessage = $this->buildCompanyDetailsMessage($company);
        $result = $whatsappService->sendSessionMessage($this->phoneNumber, $companyDetailsMessage);

        if (! $result['success']) {
            Log::error('Failed to send company details', [
                'company_id' => $company->id,
                'error' => $result['error'],
            ]);

            throw new \Exception('WhatsApp API error: '.$result['error']);
        }

        Log::info('Company details sent successfully', [
            'company_id' => $company->id,
        ]);

        // Step 2: Send brochure if available
        if ($company->exhibitor?->brochure_path) {
            $brochureUrl = url(\Storage::url($company->exhibitor->brochure_path));
            $brochureMessage = "Here is the company brochure for *{$company->company_name}*.";

            $brochureResult = $whatsappService->sendSessionMessage($this->phoneNumber, $brochureMessage, $brochureUrl);

            if ($brochureResult['success']) {
                Log::info('Company brochure sent successfully', [
                    'company_id' => $company->id,
                ]);
            } else {
                Log::error('Failed to send company brochure', [
                    'company_id' => $company->id,
                    'error' => $brochureResult['error'],
                ]);
            }
        }

        // Step 3: Dispatch individual jobs for each project with delays
        if ($company->exhibitor && $company->exhibitor->projects->isNotEmpty()) {
            $projectJobs = [];

            foreach ($company->exhibitor->projects as $index => $project) {
                $projectJobs[] = (new SendProjectDetailsMessage(
                    projectId: $project->id,
                    phoneNumber: $this->phoneNumber,
                    projectNumber: $index + 1,
                    companyName: $company->company_name
                ))->delay(now()->addSeconds($index + 1));
            }

            Bus::dispatchChain($projectJobs);

            Log::info('Project details jobs dispatched', [
                'company_id' => $company->id,
                'project_count' => count($projectJobs),
            ]);
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::critical('Company details job failed after all retries', [
            'company_id' => $this->companyId,
            'phone_number' => $this->phoneNumber,
            'exception' => $exception->getMessage(),
        ]);
    }

    protected function buildCompanyDetailsMessage(Company $company): string
    {
        $details = [];

        // Opening greeting
        $details[] = "Thank you for visiting *{$company->company_name}* at CREDAI GLAM Property Show.";
        $details[] = '';
        $details[] = 'Here are the company details:';
        $details[] = '';

        // Company Information
        $details[] = '*Company Information:*';

        if ($company->main_person_name) {
            $details[] = "Contact Person: {$company->main_person_name}";
        }

        if ($company->stall_number) {
            $details[] = "Stall Number: {$company->stall_number}";
        }

        if ($company->exhibitor) {
            if ($company->exhibitor->office_address) {
                $details[] = "Address: {$company->exhibitor->office_address}";
            }

            if ($company->exhibitor->city) {
                $details[] = "City: {$company->exhibitor->city}";
            }

            if ($company->exhibitor->email) {
                $details[] = "Email: {$company->exhibitor->email}";
            }

            if ($company->exhibitor->website) {
                $details[] = "Website: {$company->exhibitor->website}";
            }
        }

        $details[] = '';
        $details[] = 'Our team will contact you soon with more information.';

        return implode("\n", $details);
    }
}
