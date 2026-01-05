<?php

namespace App\Jobs;

use App\Models\Company;
use App\Services\WhatsAppService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
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

        // Build company details message
        $detailsMessage = $this->buildCompanyDetailsMessage($company);

        // Get brochure URL if available
        $brochureUrl = $company->exhibitor?->brochure_path
            ? \Storage::url($company->exhibitor->brochure_path)
            : null;

        // Convert to absolute URL if brochure exists
        if ($brochureUrl) {
            $brochureUrl = url($brochureUrl);
        }

        // Send company details with brochure if available
        $result = $whatsappService->sendSessionMessage($this->phoneNumber, $detailsMessage, $brochureUrl);

        if ($result['success']) {
            Log::info('Company details sent successfully', [
                'company_id' => $company->id,
                'company_name' => $company->company_name,
                'phone_number' => $this->phoneNumber,
                'response' => $result['data'] ?? $result['body'],
            ]);
        } else {
            Log::error('Failed to send company details', [
                'company_id' => $company->id,
                'company_name' => $company->company_name,
                'phone_number' => $this->phoneNumber,
                'error' => $result['error'],
            ]);

            throw new \Exception('WhatsApp API error: '.$result['error']);
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
        $details[] = 'Here are the details for the company and upcoming projects:';
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

        // Projects Information
        if ($company->exhibitor && $company->exhibitor->projects->isNotEmpty()) {
            $details[] = '';
            $details[] = '*Upcoming Projects:*';

            foreach ($company->exhibitor->projects as $index => $project) {
                $projectNumber = $index + 1;
                $details[] = '';
                $details[] = "{$projectNumber}. *{$project->name}*";

                if ($project->area) {
                    $details[] = "   Location: {$project->area}";
                }

                if ($project->category) {
                    $details[] = "   Category: {$project->category}";
                }

                if ($project->budget_range) {
                    $details[] = "   Budget: {$project->budget_range}";
                }

                if ($project->status) {
                    $details[] = "   Status: {$project->status}";
                }
            }
        }

        $details[] = '';
        $details[] = 'Our team will contact you soon with more information.';

        return implode("\n", $details);
    }
}
