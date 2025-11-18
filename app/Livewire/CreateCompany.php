<?php

namespace App\Livewire;

use App\Models\Company;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;

class CreateCompany extends Component
{
    public string $company_name = '';

    public string $registered_number = '';

    public string $main_person_name = '';

    public string $stall_type = '';

    public string $stall_number = '';

    public string $stall_size = '';

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

        // Generate registration token
        $validated['registration_token'] = Company::generateRegistrationToken();

        $company = Company::create($validated);

        Flux::toast(
            heading: 'Company created',
            text: "{$this->company_name} has been created successfully. Registration link generated.",
            variant: 'success'
        );

        $this->redirect(route('companies.edit', $company), navigate: true);
    }

    #[Title('Create Company')]
    public function render()
    {
        return view('livewire.create-company');
    }
}
