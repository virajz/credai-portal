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

    public string $total_payment = '';

    public string $payment_received = '';

    public string $payment_pending = '';

    public function updatedTotalPayment(): void
    {
        $this->calculatePaymentPending();
    }

    public function updatedPaymentReceived(): void
    {
        $this->calculatePaymentPending();
    }

    private function calculatePaymentPending(): void
    {
        $total = (float) ($this->total_payment ?: 0);
        $received = (float) ($this->payment_received ?: 0);
        $this->payment_pending = (string) ($total - $received);
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
            'total_payment' => ['nullable', 'numeric', 'min:0'],
            'payment_received' => ['nullable', 'numeric', 'min:0'],
            'payment_pending' => ['nullable', 'numeric'],
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
