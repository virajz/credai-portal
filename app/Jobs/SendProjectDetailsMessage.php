<?php

namespace App\Jobs;

use App\Models\Project;
use App\Services\WhatsAppService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendProjectDetailsMessage implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $maxExceptions = 2;

    public int $timeout = 30;

    public function __construct(
        public int $projectId,
        public string $phoneNumber,
        public int $projectNumber,
        public string $companyName
    ) {}

    public function handle(WhatsAppService $whatsappService): void
    {
        $project = Project::find($this->projectId);

        if (! $project) {
            Log::error('Project not found for sending details', [
                'project_id' => $this->projectId,
                'phone_number' => $this->phoneNumber,
            ]);

            return;
        }

        Log::info('Attempting to send project details', [
            'project_id' => $project->id,
            'project_name' => $project->name,
            'phone_number' => $this->phoneNumber,
        ]);

        // Send project details message
        $projectMessage = $this->buildProjectDetailsMessage($project);
        $result = $whatsappService->sendSessionMessage($this->phoneNumber, $projectMessage);

        if (! $result['success']) {
            Log::error('Failed to send project details', [
                'project_id' => $project->id,
                'error' => $result['error'],
            ]);

            throw new \Exception('WhatsApp API error: '.$result['error']);
        }

        Log::info('Project details sent successfully', [
            'project_id' => $project->id,
        ]);

        // Send project brochure if available
        if ($project->brochure_path) {
            $brochureUrl = url(\Storage::url($project->brochure_path));
            $brochureMessage = "Here is the brochure for *{$project->name}*.";

            $brochureResult = $whatsappService->sendSessionMessage($this->phoneNumber, $brochureMessage, $brochureUrl);

            if ($brochureResult['success']) {
                Log::info('Project brochure sent successfully', [
                    'project_id' => $project->id,
                ]);
            } else {
                Log::error('Failed to send project brochure', [
                    'project_id' => $project->id,
                    'error' => $brochureResult['error'],
                ]);
            }
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::critical('Project details job failed after all retries', [
            'project_id' => $this->projectId,
            'phone_number' => $this->phoneNumber,
            'exception' => $exception->getMessage(),
        ]);
    }

    protected function buildProjectDetailsMessage(Project $project): string
    {
        $details = [];

        $details[] = "*Project {$this->projectNumber}: {$project->name}*";
        $details[] = '';

        if ($project->area) {
            $details[] = "Location: {$project->area}";
        }

        if ($project->category) {
            $details[] = "Category: {$project->category}";
        }

        if ($project->sq_ft) {
            $details[] = "Size: {$project->sq_ft}";
        }

        if ($project->status) {
            $details[] = "Status: {$project->status}";
        }

        if ($project->handover_date) {
            $details[] = "Handover: {$project->handover_date}";
        }

        if ($project->usp) {
            $details[] = '';
            $details[] = "USP: {$project->usp}";
        }

        return implode("\n", $details);
    }
}
