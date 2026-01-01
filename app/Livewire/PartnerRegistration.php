<?php

namespace App\Livewire;

use App\Models\Partner;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.front')]
class PartnerRegistration extends Component
{
    // Tracking medium
    public string $tracking_medium = '';

    // Submission tracking
    public bool $isSubmitting = false;

    // Personal Information
    public string $first_name = '';

    public string $last_name = '';

    public string $firm_name = '';

    public string $email = '';

    public string $phone = '';

    // Interests & Preferences
    public array $areas = [];

    public array $property_types = [];

    public function mount(): void
    {
        $this->tracking_medium = request()->query('medium', '');
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'firm_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|digits:10',
            'areas' => 'required|array|min:1',
            'areas.*' => 'string',
            'property_types' => 'required|array|min:1',
            'property_types.*' => 'in:Apartment,Bungalows,Plot,Farm House,Shops,Showrooms,Office Spaces',
        ];
    }

    public function submit(): void
    {
        // Prevent double submission
        if ($this->isSubmitting) {
            return;
        }

        $this->isSubmitting = true;

        try {
            $this->validate();

            // Create partner record
            $partner = Partner::create([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'firm_name' => $this->firm_name,
                'email' => $this->email,
                'phone' => $this->phone,
                'areas' => $this->areas,
                'property_types' => $this->property_types,
                'tracking_medium' => $this->tracking_medium,
            ]);

            // Redirect to success page with QR code
            $this->redirect(route('partner.success', $partner), navigate: true);
        } catch (\Exception $e) {
            $this->isSubmitting = false;
            throw $e;
        }
    }

    #[Title('Partner Registration - CREDAI Glam Property Show 2026')]
    public function render()
    {
        return view('livewire.partner-registration');
    }
}
