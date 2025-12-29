<?php

namespace App\Livewire;

use App\Models\Visitor;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.front')]
class VisitorRegistration extends Component
{
    // Step tracking
    public int $currentStep = 1;

    // Tracking medium
    public string $tracking_medium = '';

    // Submission tracking
    public bool $isSubmitting = false;

    // Step 1: Personal Information
    public string $name = '';

    public string $phone = '';

    public string $age_group = '';

    public string $current_residential_area = '';

    public string $company_name = '';

    // Step 2: Interests & Preferences
    public array $interests = [];

    public array $residential_types = [];

    public array $commercial_types = [];

    public array $plotting_types = [];

    public array $weekend_home_types = [];

    public string $planning_to_buy = '';

    public array $areas = [];

    public function mount(): void
    {
        $this->tracking_medium = request()->query('medium', '');
    }

    public function rules(): array
    {
        $rules = [];

        if ($this->currentStep == 1) {
            $rules = [
                'name' => 'required|string|max:255',
                'phone' => 'required|digits:10|unique:visitors,phone',
                'age_group' => 'required|in:18-25,26-35,36-45,46-55,56-65,65+',
                'current_residential_area' => 'required|string|max:255',
                'company_name' => 'nullable|string|max:255',
            ];
        }

        if ($this->currentStep == 2) {
            $rules = [
                'interests' => 'required|array|min:1',
                'interests.*' => 'in:Residential,Commercial,Plotting,Weekend Home & Others',
                'planning_to_buy' => 'required|in:Within 3 months,Within 6 months,Within a year',
                'areas' => 'required|array|min:1',
                'areas.*' => 'string',
                'residential_types.*' => 'in:2 BHK,3 BHK,4 BHK,5 BHK,Others',
                'commercial_types.*' => 'in:Showroom,Shops,Offices,Others',
                'plotting_types.*' => 'in:Industrial,Residential',
            ];

            // Require subtypes when main property type is selected
            if (in_array('Residential', $this->interests)) {
                $rules['residential_types'] = 'required|array|min:1';
            }
            if (in_array('Commercial', $this->interests)) {
                $rules['commercial_types'] = 'required|array|min:1';
            }
            if (in_array('Plotting', $this->interests)) {
                $rules['plotting_types'] = 'required|array|min:1';
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'phone.unique' => 'This number is already used for registration.',
        ];
    }

    public function nextStep(): void
    {
        $this->validate();

        if ($this->currentStep < 2) {
            $this->currentStep++;
        }
    }

    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
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

            // Double-check phone uniqueness to prevent race conditions
            if (Visitor::where('phone', $this->phone)->exists()) {
                $this->addError('phone', 'This number is already used for registration.');
                $this->isSubmitting = false;

                return;
            }

            // Create visitor record
            $visitor = Visitor::create([
                'name' => $this->name,
                'phone' => $this->phone,
                'age_group' => $this->age_group,
                'current_residential_area' => $this->current_residential_area,
                'company_name' => $this->company_name,
                'interests' => $this->interests,
                'residential_types' => $this->residential_types,
                'commercial_types' => $this->commercial_types,
                'plotting_types' => $this->plotting_types,
                'weekend_home_types' => $this->weekend_home_types,
                'planning_to_buy' => $this->planning_to_buy,
                'areas' => $this->areas,
                'tracking_medium' => $this->tracking_medium,
            ]);

            // Redirect to success page with QR code
            $this->redirect(route('visitor.success', $visitor), navigate: true);
        } catch (\Exception $e) {
            $this->isSubmitting = false;
            throw $e;
        }
    }

    #[Title('Visitor Registration - CREDAI Glam Property Show 2026')]
    public function render()
    {
        return view('livewire.visitor-registration');
    }
}
