<?php

namespace App\Livewire;

use App\Models\Company;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;

class EditCompany extends Component
{
    public Company $company;

    public string $company_name = '';

    public string $registered_number = '';

    public string $main_person_name = '';

    public string $stall_type = '';

    public string $stall_number = '';

    public string $stall_size = '';

    public string $total_payment = '';

    public string $payment_received = '';

    public string $payment_pending = '';

    public function mount(Company $company): void
    {
        $this->company = $company;
        $this->company_name = $company->company_name;
        $this->registered_number = $company->registered_number;
        $this->main_person_name = $company->main_person_name;
        $this->stall_type = $company->stall_type ?? '';
        $this->stall_number = $company->stall_number ?? '';
        $this->stall_size = $company->stall_size ?? '';
        $this->total_payment = $company->total_payment ? (string) $company->total_payment : '';
        $this->payment_received = $company->payment_received ? (string) $company->payment_received : '';
        $this->payment_pending = $company->payment_pending ? (string) $company->payment_pending : '';
    }

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

        $this->company->update($validated);

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
