<?php

namespace App\Livewire;

use App\Models\ActivityLog;
use App\Models\Company;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;

class EditCompany extends Component
{
    public Company $company;

    public string $company_name = '';

    public string $category = 'Builders';

    public string $registered_number = '';

    public string $main_person_name = '';

    public string $stall_type = '';

    public string $stall_number = '';

    public string $stall_size = '';

    public bool $copied = false;

    public function mount(Company $company): void
    {
        $this->company = $company;
        $this->company_name = $company->company_name;
        $this->category = $company->category ?? 'Builders';
        $this->registered_number = $company->registered_number;
        $this->main_person_name = $company->main_person_name;
        $this->stall_type = $company->stall_type ?? '';
        $this->stall_number = $company->stall_number ?? '';
        $this->stall_size = $company->stall_size ?? '';

        // Ensure company has a registration token
        if (! $company->registration_token) {
            $company->update([
                'registration_token' => Company::generateRegistrationToken(),
            ]);
            $this->company->refresh();
        }
    }

    public function copyRegistrationLink(): void
    {
        $this->copied = true;
        $this->dispatch('registration-link-copied');
    }

    public function save(): void
    {
        $validated = $this->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:Builders,Allied'],
            'registered_number' => ['required', 'string', 'max:255'],
            'main_person_name' => ['required', 'string', 'max:255'],
            'stall_type' => ['nullable', 'string', 'max:255'],
            'stall_number' => ['nullable', 'string', 'max:255'],
            'stall_size' => ['nullable', 'string', 'max:255'],
        ]);

        $this->company->update($validated);

        ActivityLog::log(
            'company_updated',
            "Updated company: {$this->company->company_name}",
            ['company_id' => $this->company->id, 'company_name' => $this->company->company_name]
        );

        Flux::toast(
            heading: 'Company updated',
            text: "{$this->company_name} has been updated successfully.",
            variant: 'success'
        );

        $this->redirect(route('companies.index'), navigate: true);
    }

    #[Title('Edit Company')]
    public function render()
    {
        return view('livewire.edit-company');
    }
}
