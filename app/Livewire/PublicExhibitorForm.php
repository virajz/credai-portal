<?php

namespace App\Livewire;

use App\Models\Company;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.public')]
class PublicExhibitorForm extends ExhibitorForm
{
    // Company selection
    public ?int $selected_company_id = null;

    public string $entered_registered_number = '';

    public bool $company_verified = false;

    public ?Company $selectedCompany = null;

    /**
     * Initialize public form
     */
    public function mount(): void
    {
        // Set current step to 0 for company selection before calling parent
        $this->currentStep = 0;

        parent::mount();

        // If resuming a draft with company already verified, restore that state
        if ($this->draftId) {
            $draft = \App\Models\DraftExhibitor::find($this->draftId);
            if ($draft && $draft->company_id) {
                $this->selected_company_id = $draft->company_id;
                $this->selectedCompany = Company::find($draft->company_id);
                $this->company_verified = true;
                $this->currentStep = max($this->currentStep, 1);

                // Restore company data to form fields from the company (not draft)
                if ($this->selectedCompany) {
                    $this->brand_name = $this->selectedCompany->company_name;
                    $this->contact_person_name = $this->selectedCompany->main_person_name ?? '';
                    $this->phone_number = $this->selectedCompany->registered_number;

                    // Restore stall details from company
                    $this->stall_type = $this->selectedCompany->stall_type ?? '';
                    $this->stall_number = $this->selectedCompany->stall_number ?? '';
                    $this->stall_size = $this->selectedCompany->stall_size ?? '';
                    $this->total_payment = $this->selectedCompany->total_payment ? (string) $this->selectedCompany->total_payment : '';
                    $this->payment_received = $this->selectedCompany->payment_received ? (string) $this->selectedCompany->payment_received : '';
                    $this->payment_pending = $this->selectedCompany->payment_pending ? (string) $this->selectedCompany->payment_pending : '';
                }
            }
        }
    }

    /**
     * Verify company selection and continue
     */
    public function verifyCompany(): void
    {
        $this->validate([
            'selected_company_id' => ['required', 'exists:companies,id'],
            'entered_registered_number' => ['required', 'string', 'digits:10'],
        ]);

        $this->selectedCompany = Company::find($this->selected_company_id);

        // Verify the registered number matches
        if ($this->selectedCompany->registered_number !== $this->entered_registered_number) {
            $this->addError('entered_registered_number', 'The registered number does not match our records.');

            return;
        }
        $this->company_verified = true;

        // Auto-fill exhibitor details from company
        $this->brand_name = $this->selectedCompany->company_name;
        $this->contact_person_name = $this->selectedCompany->main_person_name ?? '';
        $this->phone_number = $this->selectedCompany->registered_number;

        // Auto-fill stall details from company
        $this->stall_type = $this->selectedCompany->stall_type ?? '';
        $this->stall_number = $this->selectedCompany->stall_number ?? '';
        $this->stall_size = $this->selectedCompany->stall_size ?? '';
        $this->total_payment = $this->selectedCompany->total_payment ? (string) $this->selectedCompany->total_payment : '';
        $this->payment_received = $this->selectedCompany->payment_received ? (string) $this->selectedCompany->payment_received : '';
        $this->payment_pending = $this->selectedCompany->payment_pending ? (string) $this->selectedCompany->payment_pending : '';

        // Save company_id to draft
        $this->ensureDraftExists();
        \App\Models\DraftExhibitor::where('id', $this->draftId)->update([
            'company_id' => $this->selected_company_id,
            'brand_name' => $this->brand_name,
            'contact_person_name' => $this->contact_person_name,
            'phone_number' => $this->phone_number,
            'stall_type' => $this->stall_type ?: null,
            'stall_number' => $this->stall_number ?: null,
            'stall_size' => $this->stall_size ?: null,
            'total_payment' => $this->total_payment ?: null,
            'payment_received' => $this->payment_received ?: null,
            'payment_pending' => $this->payment_pending ?: null,
        ]);

        // Move to step 1
        $this->currentStep = 1;

        Flux::toast(
            heading: 'Company Selected',
            text: 'You can now continue with the exhibitor registration.',
            variant: 'success'
        );
    }

    /**
     * Check if stall details should be read-only
     */
    public function isStallDetailsReadOnly(): bool
    {
        return $this->company_verified;
    }

    /**
     * Check if this is a public form (not authenticated)
     */
    public function isPublicForm(): bool
    {
        return true;
    }

    /**
     * Get the redirect route after successful submission
     */
    protected function getRedirectRoute(): string
    {
        return route('exhibitor.public.thank-you');
    }

    /**
     * Get the success message after submission
     */
    protected function getSuccessMessage(): string
    {
        return 'Thank you! Your exhibitor registration is complete. We will contact you shortly via WhatsApp.';
    }

    /**
     * Override saveDraft to include company_id
     */
    public function saveDraft(): void
    {
        parent::saveDraft();

        if ($this->draftId && $this->selected_company_id) {
            \App\Models\DraftExhibitor::where('id', $this->draftId)->update([
                'company_id' => $this->selected_company_id,
            ]);
        }
    }

    #[Title('Exhibitor Registration - CREDAI')]
    public function render()
    {
        $companies = Company::orderBy('company_name')->get();

        return view('livewire.public-exhibitor-form', [
            'companies' => $companies,
        ]);
    }
}
