<?php

namespace App\Livewire;

use App\Models\Company;
use Livewire\Attributes\Title;
use Livewire\Component;

class CompanySubmission extends Component
{
    public Company $company;

    public function mount(Company $company): void
    {
        // Check if company has a submission
        if (! $company->has_submitted) {
            abort(404, 'No submission found for this company');
        }

        $this->company = $company->load(['exhibitor.projects', 'draftExhibitor']);
    }

    #[Title('Company Submission')]
    public function render()
    {
        return view('livewire.company-submission');
    }
}
