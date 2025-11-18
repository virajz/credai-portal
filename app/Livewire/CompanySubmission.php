<?php

namespace App\Livewire;

use App\Models\Company;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

class CompanySubmission extends Component
{
    public Company $company;

    #[Url]
    public string $tab = 'overview';

    public ?int $selectedProjectIndex = null;

    public function mount(Company $company): void
    {
        // Check if company has a submission
        if (! $company->has_submitted) {
            abort(404, 'No submission found for this company');
        }

        $this->company = $company->load(['exhibitor.projects', 'draftExhibitor']);
    }

    public function selectProject(int $index): void
    {
        $this->selectedProjectIndex = $index;
    }

    #[Title('Company Submission')]
    public function render()
    {
        return view('livewire.company-submission');
    }
}
