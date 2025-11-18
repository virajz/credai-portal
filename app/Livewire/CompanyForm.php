<?php

namespace App\Livewire;

use App\Models\Company;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;

class CompanyForm extends Component
{
    public ?Company $company = null;

    public string $company_name = '';

    public string $registered_number = '';

    public string $main_person_name = '';

    public string $stall_type = '';

    public string $stall_number = '';

    public string $stall_size = '';

    public function mount(?Company $company = null): void
    {
        if ($company && $company->exists) {
            $this->company = $company;
            $this->company_name = $company->company_name;
            $this->registered_number = $company->registered_number;
            $this->main_person_name = $company->main_person_name;
            $this->stall_type = $company->stall_type ?? '';
            $this->stall_number = $company->stall_number ?? '';
            $this->stall_size = $company->stall_size ?? '';
        }
    }

    public function save(): void
    {
        $validated = $this->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'registered_number' => ['required', 'string', 'max:255'],
            'main_person_name' => ['required', 'string', 'max:255'],
            'stall_type' => ['nullable', 'string', 'max:255'],
            'stall_number' => ['nullable', 'string', 'max:255'],
            'stall_size' => ['nullable', 'string', 'max:255'],
        ]);

        if ($this->company && $this->company->exists) {
            $this->company->update($validated);
            $message = "{$this->company_name} has been updated successfully.";
        } else {
            Company::create($validated);
            $message = "{$this->company_name} has been created successfully.";
        }

        Flux::toast(
            heading: $this->company && $this->company->exists ? 'Company updated' : 'Company created',
            text: $message,
            variant: 'success'
        );

        $this->redirect(route('companies.index'), navigate: true);
    }

    #[Title('Company Form')]
    public function render()
    {
        return view('livewire.company-form')->with('company', $this->company);
    }
}
