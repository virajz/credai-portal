<?php

namespace App\Livewire;

use App\Models\DraftExhibitor;
use App\Models\Exhibitor;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;

class ExhibitorForm extends Component
{
    use WithFileUploads;

    // Step tracking
    public int $currentStep = 1;

    public array $completedSteps = [];

    // Draft functionality
    #[Url(as: 'resume')]
    public ?string $resumeToken = null;

    public ?int $draftId = null;

    public bool $showResumeLink = false;

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
     * Component initialization
     */
    public function mount(): void
    {
        // Check if resume token is set via URL (Livewire's #[Url] attribute)
        if ($this->resumeToken) {
            $this->loadDraft($this->resumeToken);
        } else {
            // Check if user has a draft in session
            $sessionToken = session('exhibitor_draft_token');
            if ($sessionToken) {
                $this->resumeToken = $sessionToken;
                $this->loadDraft($sessionToken);
            }
        }
        // Don't create a new draft on mount - wait until user starts filling
    }

    /**
     * Create a new draft entry (called lazily when needed)
     */
    protected function createNewDraft(): void
    {
        $draft = DraftExhibitor::create([
            'resume_token' => DraftExhibitor::generateResumeToken(),
            'last_activity_at' => now(),
        ]);

        $this->draftId = $draft->id;
        $this->resumeToken = $draft->resume_token;
        $this->showResumeLink = true;

        // Store in session so user can refresh without losing draft
        session(['exhibitor_draft_token' => $draft->resume_token]);

        // The #[Url] attribute will automatically update the browser URL
    }

    /**
     * Ensure a draft exists before saving
     */
    protected function ensureDraftExists(): void
    {
        if (! $this->draftId) {
            $this->createNewDraft();
        }
    }

    /**
     * Load draft data from resume token
     */
    protected function loadDraft(string $token): void
    {
        $draft = DraftExhibitor::where('resume_token', $token)
            ->where('is_completed', false)
            ->first();

        if ($draft) {
            $this->draftId = $draft->id;
            $this->resumeToken = $draft->resume_token;
            $this->brand_name = $draft->brand_name ?? '';
            $this->office_address = $draft->office_address ?? '';
            $this->city = $draft->city ?? '';
            $this->contact_person_name = $draft->contact_person_name ?? '';
            $this->phone_number = $draft->phone_number ?? '';
            $this->email = $draft->email ?? '';
            $this->website = $draft->website ?? '';
            $this->video_url = $draft->video_url ?? '';
            $this->social_media_links = $draft->social_media_links ?? [
                'facebook' => '',
                'linkedin' => '',
                'instagram' => '',
            ];
            $this->facia_name = $draft->facia_name ?? '';
            $this->currentStep = $draft->current_step ?? 1;
            $this->completedSteps = $draft->completed_steps ?? [];
            $this->showResumeLink = true;
        } else {
            // Invalid or expired resume token - start fresh
            $this->createNewDraft();
            session()->flash('warning', 'The resume link was invalid or expired. Starting a new form.');
        }
    }

    /**
     * Auto-save the draft whenever relevant data changes
     */
    public function updated($propertyName): void
    {
        // Skip file uploads and internal properties
        if (in_array($propertyName, ['logo', 'brochure', 'photos', 'photo_labels', 'use_brand_name_as_facia', 'currentStep', 'completedSteps', 'showResumeLink'])) {
            return;
        }

        $this->saveDraft();
    }

    /**
     * Save the current form state as a draft
     */
    public function saveDraft(): void
    {
        // Create draft if it doesn't exist yet
        $this->ensureDraftExists();

        DraftExhibitor::where('id', $this->draftId)->update([
            'brand_name' => $this->brand_name ?: null,
            'office_address' => $this->office_address ?: null,
            'city' => $this->city ?: null,
            'contact_person_name' => $this->contact_person_name ?: null,
            'phone_number' => $this->phone_number ?: null,
            'email' => $this->email ?: null,
            'website' => $this->website ?: null,
            'video_url' => $this->video_url ?: null,
            'social_media_links' => array_filter($this->social_media_links) ?: null,
            'facia_name' => $this->facia_name ?: null,
            'current_step' => $this->currentStep,
            'completed_steps' => $this->completedSteps,
            'last_activity_at' => now(),
        ]);
    }

    /**
     * Copy the resume link to clipboard
     */
    public function copyResumeLink(): void
    {
        $this->dispatch('resume-link-copied');
    }

    /**
     * Get the resume URL
     */
    public function getResumeUrlProperty(): string
    {
        if (! $this->resumeToken) {
            return '';
        }

        return route('exhibitor.public.register', ['resume' => $this->resumeToken]);
    }

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
            $this->saveDraft();
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

            $this->saveDraft();
        }
    }

    /**
     * Move to previous step
     */
    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
            $this->saveDraft();
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
                'phone_number' => ['required', 'digits:10'],
                'email' => ['nullable', 'email', 'max:255'],
                'website' => ['nullable', 'url', 'max:255'],
            ], [
                'contact_person_name.required' => 'Please provide the main contact person\'s name.',
                'phone_number.required' => 'Mobile number is required for event coordination.',
                'phone_number.digits' => 'Mobile number must be exactly 10 digits.',
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
            'phone_number' => ['required', 'digits:10'],
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
            'phone_number.digits' => 'Mobile number must be exactly 10 digits.',
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

        // Mark draft as completed
        if ($this->draftId) {
            DraftExhibitor::where('id', $this->draftId)->update([
                'is_completed' => true,
            ]);
        }

        // Clear the draft token from session
        session()->forget('exhibitor_draft_token');

        session()->flash('success', $this->getSuccessMessage());

        $this->redirect($this->getRedirectRoute(), navigate: true);
    }

    #[Title('Exhibitor Information Form')]
    public function render()
    {
        return view('livewire.exhibitor-form');
    }
}
