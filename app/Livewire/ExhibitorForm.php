<?php

namespace App\Livewire;

use App\Models\Exhibitor;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

class ExhibitorForm extends Component
{
    use WithFileUploads;

    // Step tracking
    public int $currentStep = 1;

    public array $completedSteps = [];

    public array $cities = [
        'Surat',
        'Navsari',
        'Ahmedabad',
        'Baroda',
        'Others',
    ];

    // Company Details
    public string $brand_name = '';

    public string $office_address = '';

    public string $city = '';

    // Contact Person
    public string $contact_person_name = '';

    public string $phone_number = '';

    public string $email = '';

    public string $website = '';

    // Branding & Media
    public $logo;

    public $brochure;

    public array $photos = [];

    public array $photo_labels = [];

    public string $video_url = '';

    public array $social_media_links = [
        'facebook' => '',
        'linkedin' => '',
        'instagram' => '',
    ];

    // Exhibition Display
    public string $facia_name = '';

    public bool $use_brand_name_as_facia = false;

    /**
     * Remove a photo from the list
     */
    public function removePhoto(int $index): void
    {
        if (isset($this->photos[$index])) {
            $photo = $this->photos[$index];
            $photo->delete();
            unset($this->photos[$index]);
            unset($this->photo_labels[$index]);
            $this->photos = array_values($this->photos);
            $this->photo_labels = array_values($this->photo_labels);
        }
    }

    /**
     * Remove the logo
     */
    public function removeLogo(): void
    {
        if ($this->logo) {
            $this->logo->delete();
            $this->logo = null;
        }
    }

    /**
     * Remove the brochure
     */
    public function removeBrochure(): void
    {
        if ($this->brochure) {
            $this->brochure->delete();
            $this->brochure = null;
        }
    }

    /**
     * Update facia name when switch is toggled
     */
    public function updatedUseBrandNameAsFacia($value): void
    {
        if ($value) {
            $this->facia_name = strtoupper($this->brand_name);
        }
    }

    /**
     * Navigate to a specific step
     */
    public function goToStep(int $step): void
    {
        if ($step >= 1 && $step <= 4) {
            $this->currentStep = $step;
        }
    }

    /**
     * Validate current step and move to next
     */
    public function nextStep(): void
    {
        $this->validateCurrentStep();

        if (! $this->getErrorBag()->any()) {
            $this->completedSteps[] = $this->currentStep;
            $this->completedSteps = array_unique($this->completedSteps);

            if ($this->currentStep < 4) {
                $this->currentStep++;
            }
        }
    }

    /**
     * Move to previous step
     */
    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    /**
     * Validate the current step
     */
    protected function validateCurrentStep(): void
    {
        match ($this->currentStep) {
            1 => $this->validate([
                'brand_name' => ['required', 'string', 'max:255'],
                'office_address' => ['required', 'string', 'max:1000'],
                'city' => ['required', 'string', 'in:Surat,Navsari,Ahmedabad,Baroda,Others'],
            ], [
                'brand_name.required' => 'Please enter your company or brand name.',
                'office_address.required' => 'Company address is required for CREDAI records.',
                'city.required' => 'Please select your main business location.',
                'city.in' => 'Please select a valid city from the dropdown.',
            ]),
            2 => $this->validate([
                'contact_person_name' => ['required', 'string', 'max:255'],
                'phone_number' => ['required', 'string', 'max:20'],
                'email' => ['nullable', 'email', 'max:255'],
                'website' => ['nullable', 'url', 'max:255'],
            ], [
                'contact_person_name.required' => 'Please provide the main contact person\'s name.',
                'phone_number.required' => 'Mobile number is required for event coordination.',
                'email.email' => 'Please provide a valid email address.',
                'website.url' => 'Please provide a valid website URL.',
            ]),
            3 => $this->validate([
                'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
                'brochure' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
                'photos' => ['required', 'array', 'min:3', 'max:5'],
                'photos.*' => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
                'photo_labels' => ['nullable', 'array'],
                'photo_labels.*' => ['nullable', 'string', 'max:255'],
                'video_url' => ['nullable', 'url', 'max:255'],
                'social_media_links' => ['nullable', 'array'],
                'social_media_links.facebook' => ['nullable', 'url', 'max:255'],
                'social_media_links.linkedin' => ['nullable', 'url', 'max:255'],
                'social_media_links.instagram' => ['nullable', 'url', 'max:255'],
            ], [
                'logo.image' => 'Logo must be an image file.',
                'logo.mimes' => 'Logo must be a PNG or JPG file.',
                'logo.max' => 'Logo file size should not exceed 2MB.',
                'brochure.mimes' => 'Brochure must be a PDF file.',
                'brochure.max' => 'Brochure file size should not exceed 10MB.',
                'photos.required' => 'Please upload at least 3 photos to showcase your company or projects.',
                'photos.min' => 'Please upload at least 3 photos to showcase your company or projects.',
                'photos.max' => 'You can upload a maximum of 5 photos.',
                'photos.*.image' => 'All photos must be image files.',
                'photos.*.mimes' => 'Photos must be PNG or JPG files.',
                'photos.*.max' => 'Each photo should not exceed 2MB.',
                'video_url.url' => 'Please provide a valid video URL (YouTube or Vimeo).',
                'social_media_links.facebook.url' => 'Please provide a valid Facebook URL.',
                'social_media_links.linkedin.url' => 'Please provide a valid LinkedIn URL.',
                'social_media_links.instagram.url' => 'Please provide a valid Instagram URL.',
            ]),
            4 => $this->validate([
                'facia_name' => ['required', 'string', 'max:255'],
            ], [
                'facia_name.required' => 'Please provide the name for your booth fascia board.',
            ]),
            default => null,
        };
    }

    /**
     * Get the redirect route after successful submission
     */
    protected function getRedirectRoute(): string
    {
        return route('dashboard');
    }

    /**
     * Get the success message after submission
     */
    protected function getSuccessMessage(): string
    {
        return 'Exhibitor information submitted successfully!';
    }

    /**
     * Submit the exhibitor form
     */
    public function submit(): void
    {
        $validated = $this->validate([
            // Company Details
            'brand_name' => ['required', 'string', 'max:255'],
            'office_address' => ['required', 'string', 'max:1000'],
            'city' => ['required', 'string', 'in:Surat,Navsari,Ahmedabad,Baroda,Others'],

            // Contact Person
            'contact_person_name' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],

            // Branding & Media
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'brochure' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'photos' => ['required', 'array', 'min:3', 'max:5'],
            'photos.*' => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'photo_labels' => ['nullable', 'array'],
            'photo_labels.*' => ['nullable', 'string', 'max:255'],
            'video_url' => ['nullable', 'url', 'max:255'],
            'social_media_links' => ['nullable', 'array'],
            'social_media_links.facebook' => ['nullable', 'url', 'max:255'],
            'social_media_links.linkedin' => ['nullable', 'url', 'max:255'],
            'social_media_links.instagram' => ['nullable', 'url', 'max:255'],

            // Exhibition Display
            'facia_name' => ['required', 'string', 'max:255'],
        ], [
            'brand_name.required' => 'Please enter your company or brand name.',
            'office_address.required' => 'Company address is required for CREDAI records.',
            'city.required' => 'Please select your main business location.',
            'city.in' => 'Please select a valid city from the dropdown.',
            'contact_person_name.required' => 'Please provide the main contact person\'s name.',
            'phone_number.required' => 'Mobile number is required for event coordination.',
            'email.email' => 'Please provide a valid email address.',
            'website.url' => 'Please provide a valid website URL.',
            'logo.image' => 'Logo must be an image file.',
            'logo.mimes' => 'Logo must be a PNG or JPG file.',
            'logo.max' => 'Logo file size should not exceed 2MB.',
            'brochure.mimes' => 'Brochure must be a PDF file.',
            'brochure.max' => 'Brochure file size should not exceed 10MB.',
            'photos.required' => 'Please upload at least 3 photos to showcase your company or projects.',
            'photos.min' => 'Please upload at least 3 photos to showcase your company or projects.',
            'photos.max' => 'You can upload a maximum of 5 photos.',
            'photos.*.image' => 'All photos must be image files.',
            'photos.*.mimes' => 'Photos must be PNG or JPG files.',
            'photos.*.max' => 'Each photo should not exceed 2MB.',
            'video_url.url' => 'Please provide a valid video URL (YouTube or Vimeo).',
            'social_media_links.facebook.url' => 'Please provide a valid Facebook URL.',
            'social_media_links.linkedin.url' => 'Please provide a valid LinkedIn URL.',
            'social_media_links.instagram.url' => 'Please provide a valid Instagram URL.',
            'facia_name.required' => 'Please provide the name for your booth fascia board.',
        ]);

        // Handle file uploads
        $logoPath = $this->logo ? $this->logo->store('exhibitors/logos', 'public') : null;
        $brochurePath = $this->brochure ? $this->brochure->store('exhibitors/brochures', 'public') : null;

        // Handle photos with labels
        $photosData = [];
        if (! empty($this->photos)) {
            foreach ($this->photos as $index => $photo) {
                $path = $photo->store('exhibitors/photos', 'public');
                $label = $this->photo_labels[$index] ?? '';
                $photosData[] = [
                    'path' => $path,
                    'label' => $label,
                ];
            }
        }

        // Filter empty social media links
        $socialMediaLinks = array_filter($this->social_media_links, fn($value) => ! empty($value));

        // Create exhibitor
        Exhibitor::create([
            'brand_name' => $validated['brand_name'],
            'office_address' => $validated['office_address'],
            'city' => $validated['city'],
            'contact_person_name' => $validated['contact_person_name'],
            'phone_number' => $validated['phone_number'],
            'email' => $validated['email'] ?? null,
            'website' => $validated['website'] ?? null,
            'logo_path' => $logoPath,
            'brochure_path' => $brochurePath,
            'photos' => ! empty($photosData) ? $photosData : null,
            'video_url' => $validated['video_url'] ?? null,
            'social_media_links' => ! empty($socialMediaLinks) ? $socialMediaLinks : null,
            'facia_name' => $validated['facia_name'],
        ]);

        session()->flash('success', $this->getSuccessMessage());

        $this->redirect($this->getRedirectRoute(), navigate: true);
    }

    #[Title('Exhibitor Information Form')]
    public function render()
    {
        return view('livewire.exhibitor-form');
    }
}
