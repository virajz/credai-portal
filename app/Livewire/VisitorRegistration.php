<?php

namespace App\Livewire;

use App\Models\Visitor;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.front')]
class VisitorRegistration extends Component
{
    use WithFileUploads;

    // Step tracking
    public int $currentStep = 1;

    // Step 1: Personal Information
    public string $name = '';

    public string $phone = '';

    public string $email = '';

    public string $company_name = '';

    public $photo;

    // Step 2: Interests & Preferences
    public array $interests = [];

    public array $residential_types = [];

    public array $commercial_types = [];

    public array $plotting_types = [];

    public string $planning_to_buy = '';

    public array $areas = [];

    public function rules(): array
    {
        $rules = [];

        if ($this->currentStep == 1) {
            $rules = [
                'name' => 'required|string|max:255',
                'phone' => 'required|digits:10|unique:visitors,phone',
                'email' => 'nullable|email|max:255',
                'company_name' => 'nullable|string|max:255',
                'photo' => 'nullable|image|max:2048',
            ];
        }

        if ($this->currentStep == 2) {
            $rules = [
                'interests' => 'required|array|min:1',
                'interests.*' => 'in:Residential,Commercial,Plotting',
                'planning_to_buy' => 'required|in:Within 3 months,Within 6 months,Within a year',
                'areas' => 'required|array|min:1',
                'areas.*' => 'string',
                'residential_types.*' => 'in:2 BHK,3 BHK,4 BHK,5 BHK,Others',
                'commercial_types.*' => 'in:Showroom,Shops,Offices,Others',
                'plotting_types.*' => 'in:Industrial,Open',
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
        $this->validate();

        // Handle photo upload
        $photoPath = null;
        if ($this->photo) {
            $photoPath = $this->photo->store('visitor-photos', 'public');
        }

        // Create visitor record
        Visitor::create([
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'company_name' => $this->company_name,
            'photo_path' => $photoPath,
            'interests' => $this->interests,
            'residential_types' => $this->residential_types,
            'commercial_types' => $this->commercial_types,
            'plotting_types' => $this->plotting_types,
            'planning_to_buy' => $this->planning_to_buy,
            'areas' => $this->areas,
        ]);

        // Reset form
        $this->reset([
            'name',
            'phone',
            'email',
            'company_name',
            'photo',
            'interests',
            'residential_types',
            'commercial_types',
            'plotting_types',
            'planning_to_buy',
            'areas',
            'currentStep',
        ]);

        // Show success toast
        Flux::toast(
            heading: 'Registration Successful!',
            text: 'Thank you for registering for CREDAI Glam 2026. Your visitor pass details will be sent to you soon.',
            variant: 'success'
        );
    }

    #[Title('Visitor Registration - CREDAI Glam 2026')]
    public function render()
    {
        return view('livewire.visitor-registration');
    }
}
