<?php

namespace App\Livewire;

use App\Models\DraftExhibitor;
use App\Models\Exhibitor;
use App\Models\Project;
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

    public string $gst_number = '';

    public string $pan_number = '';

    // Contact Person
    public string $contact_person_name = '';

    public string $phone_number = '';

    public string $email = '';

    public string $website = '';

    // Branding & Media
    public $logo;

    public $brochure;

    public string $video_url = '';

    public array $social_media_links = [
        'facebook' => '',
        'linkedin' => '',
        'instagram' => '',
        'youtube' => '',
    ];

    // Exhibition Display
    public string $facia_name = '';

    public string $additional_details = '';

    public bool $use_brand_name_as_facia = false;

    // Stall Details
    public string $stall_type = '';

    public string $stall_number = '';

    public string $stall_size = '';

    public string $total_payment = '';

    public string $payment_received = '';

    public string $payment_pending = '';

    public string $extra_furniture_details = '';

    public string $exhibitor_passes_details = '';

    public string $momento_name = '';

    public string $car_pass_details = '';

    // Projects
    public array $projects = [];

    public array $newProject = [
        'name' => '',
        'area' => '',
        'area_other' => '',
        'category' => '',
        'sq_ft' => '',
        'budget_range' => '',
        'handover_date' => '',
        'status' => '',
        'pdf' => null,
        'video_url' => '',
        'usp' => '',
        'contact_person' => '',
        'logo' => null,
        'photos' => [],
    ];

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
            $this->gst_number = $draft->gst_number ?? '';
            $this->pan_number = $draft->pan_number ?? '';
            $this->contact_person_name = $draft->contact_person_name ?? '';
            $this->phone_number = $draft->phone_number ?? '';
            $this->email = $draft->email ?? '';
            $this->website = $draft->website ?? '';
            $this->video_url = $draft->video_url ?? '';
            $this->social_media_links = $draft->social_media_links ?? [
                'facebook' => '',
                'linkedin' => '',
                'instagram' => '',
                'youtube' => '',
            ];
            $this->facia_name = $draft->facia_name ?? '';
            $this->additional_details = $draft->additional_details ?? '';
            $this->stall_type = $draft->stall_type ?? '';
            $this->stall_number = $draft->stall_number ?? '';
            $this->stall_size = $draft->stall_size ?? '';
            $this->total_payment = $draft->total_payment ?? '';
            $this->payment_received = $draft->payment_received ?? '';
            $this->payment_pending = $draft->payment_pending ?? '';
            $this->extra_furniture_details = $draft->extra_furniture_details ?? '';
            $this->exhibitor_passes_details = $draft->exhibitor_passes_details ?? '';
            $this->momento_name = $draft->momento_name ?? '';
            $this->car_pass_details = $draft->car_pass_details ?? '';
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
        if (in_array($propertyName, ['logo', 'brochure', 'use_brand_name_as_facia', 'currentStep', 'completedSteps', 'showResumeLink'])) {
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
            'gst_number' => $this->gst_number ?: null,
            'pan_number' => $this->pan_number ?: null,
            'contact_person_name' => $this->contact_person_name ?: null,
            'phone_number' => $this->phone_number ?: null,
            'email' => $this->email ?: null,
            'website' => $this->website ?: null,
            'video_url' => $this->video_url ?: null,
            'social_media_links' => array_filter($this->social_media_links) ?: null,
            'facia_name' => $this->facia_name ?: null,
            'additional_details' => $this->additional_details ?: null,
            'stall_type' => $this->stall_type ?: null,
            'stall_number' => $this->stall_number ?: null,
            'stall_size' => $this->stall_size ?: null,
            'total_payment' => $this->total_payment ?: null,
            'payment_received' => $this->payment_received ?: null,
            'payment_pending' => $this->payment_pending ?: null,
            'extra_furniture_details' => $this->extra_furniture_details ?: null,
            'exhibitor_passes_details' => $this->exhibitor_passes_details ?: null,
            'momento_name' => $this->momento_name ?: null,
            'car_pass_details' => $this->car_pass_details ?: null,
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
     * Check if this is a public form (not authenticated)
     */
    public function isPublicForm(): bool
    {
        return false;
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
        if ($step >= 1 && $step <= 3) {
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

            if ($this->currentStep < 3) {
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
                'gst_number' => ['nullable', 'string', 'max:255'],
                'pan_number' => ['nullable', 'string', 'max:255'],
                'contact_person_name' => ['required', 'string', 'max:255'],
                'phone_number' => ['required', 'digits:10'],
                'email' => ['nullable', 'email', 'max:255'],
                'website' => ['nullable', 'url', 'max:255'],
                'logo' => ['nullable', 'file', 'mimes:png,jpg,jpeg,pdf,cdr', 'max:5120'],
                'brochure' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
                'video_url' => ['nullable', 'url', 'max:255'],
                'social_media_links' => ['nullable', 'array'],
                'social_media_links.facebook' => ['nullable', 'url', 'max:255'],
                'social_media_links.linkedin' => ['nullable', 'url', 'max:255'],
                'social_media_links.instagram' => ['nullable', 'url', 'max:255'],
                'social_media_links.youtube' => ['nullable', 'url', 'max:255'],
                'additional_details' => ['nullable', 'string', 'max:1000'],
            ], [
                'brand_name.required' => 'Please enter your company or brand name.',
                'office_address.required' => 'Company address is required for CREDAI records.',
                'city.required' => 'Please select your main business location.',
                'city.in' => 'Please select a valid city from the dropdown.',
                'gst_number.max' => 'GST number should not exceed 255 characters.',
                'pan_number.max' => 'PAN card number should not exceed 255 characters.',
                'contact_person_name.required' => 'Please provide the main contact person\'s name.',
                'phone_number.required' => 'Mobile number is required for event coordination.',
                'phone_number.digits' => 'Mobile number must be exactly 10 digits.',
                'email.email' => 'Please provide a valid email address.',
                'website.url' => 'Please provide a valid website URL.',
                'logo.file' => 'Logo must be a file.',
                'logo.mimes' => 'Logo must be a PNG, JPG, PDF, or CDR file.',
                'logo.max' => 'Logo file size should not exceed 5MB.',
                'brochure.mimes' => 'Brochure must be a PDF file.',
                'brochure.max' => 'Brochure file size should not exceed 10MB.',
                'video_url.url' => 'Please provide a valid video URL (YouTube or Vimeo).',
                'social_media_links.facebook.url' => 'Please provide a valid Facebook URL.',
                'social_media_links.linkedin.url' => 'Please provide a valid LinkedIn URL.',
                'social_media_links.instagram.url' => 'Please provide a valid Instagram URL.',
                'social_media_links.youtube.url' => 'Please provide a valid YouTube URL.',
                'additional_details.max' => 'Additional details should not exceed 1000 characters.',
            ]),
            2 => $this->validate([
                'stall_type' => ['nullable', 'string', 'max:255'],
                'stall_number' => ['nullable', 'string', 'max:255'],
                'stall_size' => ['nullable', 'string', 'max:255'],
                'total_payment' => ['nullable', 'numeric', 'min:0'],
                'payment_received' => ['nullable', 'numeric', 'min:0'],
                'payment_pending' => ['nullable', 'numeric', 'min:0'],
                'extra_furniture_details' => ['nullable', 'string', 'max:1000'],
                'exhibitor_passes_details' => ['nullable', 'string', 'max:1000'],
                'momento_name' => ['nullable', 'string', 'max:255'],
                'facia_name' => ['required', 'string', 'max:255'],
                'car_pass_details' => ['nullable', 'string', 'max:1000'],
            ], [
                'total_payment.numeric' => 'Total payment must be a number.',
                'payment_received.numeric' => 'Payment received must be a number.',
                'payment_pending.numeric' => 'Payment pending must be a number.',
                'facia_name.required' => 'Please provide the name for your booth fascia board.',
            ]),
            3 => $this->validate([
                'projects' => ['nullable', 'array'],
            ], []),
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
     * Add a new project to the list
     */
    public function addProject(): void
    {
        $this->projects[] = [
            'name' => '',
            'area' => '',
            'category' => '',
            'sq_ft' => '',
            'budget_range' => '',
            'handover_date' => '',
            'status' => '',
            'pdf' => null,
            'video_url' => '',
            'usp' => '',
            'contact_person' => '',
            'logo' => null,
        ];
    }

    /**
     * Remove a project from the list
     */
    public function removeProject(int $index): void
    {
        unset($this->projects[$index]);
        $this->projects = array_values($this->projects);
    }

    /**
     * Remove a file from a project (PDF or logo)
     */
    public function removeProjectFile(int $index, string $type): void
    {
        if (isset($this->projects[$index][$type])) {
            unset($this->projects[$index][$type]);
        }
    }

    /**
     * Remove a photo from a project
     */
    public function removeProjectPhoto(int $projectIndex, int $photoIndex): void
    {
        if (isset($this->projects[$projectIndex]['photos'][$photoIndex])) {
            unset($this->projects[$projectIndex]['photos'][$photoIndex]);
            $this->projects[$projectIndex]['photos'] = array_values($this->projects[$projectIndex]['photos']);
        }
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
            'gst_number' => ['nullable', 'string', 'max:255'],
            'pan_number' => ['nullable', 'string', 'max:255'],

            // Contact Person
            'contact_person_name' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'digits:10'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],

            // Branding & Media
            'logo' => ['nullable', 'file', 'mimes:png,jpg,jpeg,pdf,cdr', 'max:5120'],
            'brochure' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'video_url' => ['nullable', 'url', 'max:255'],
            'social_media_links' => ['nullable', 'array'],
            'social_media_links.facebook' => ['nullable', 'url', 'max:255'],
            'social_media_links.linkedin' => ['nullable', 'url', 'max:255'],
            'social_media_links.instagram' => ['nullable', 'url', 'max:255'],
            'social_media_links.youtube' => ['nullable', 'url', 'max:255'],

            // Exhibition Display
            'facia_name' => ['required', 'string', 'max:255'],
            'additional_details' => ['nullable', 'string', 'max:1000'],
        ], [
            'brand_name.required' => 'Please enter your company or brand name.',
            'office_address.required' => 'Company address is required for CREDAI records.',
            'city.required' => 'Please select your main business location.',
            'city.in' => 'Please select a valid city from the dropdown.',
            'gst_number.max' => 'GST number should not exceed 255 characters.',
            'pan_number.max' => 'PAN card number should not exceed 255 characters.',
            'contact_person_name.required' => 'Please provide the main contact person\'s name.',
            'phone_number.required' => 'Mobile number is required for event coordination.',
            'phone_number.digits' => 'Mobile number must be exactly 10 digits.',
            'email.email' => 'Please provide a valid email address.',
            'website.url' => 'Please provide a valid website URL.',
            'logo.file' => 'Logo must be a file.',
            'logo.mimes' => 'Logo must be a PNG, JPG, PDF, or CDR file.',
            'logo.max' => 'Logo file size should not exceed 5MB.',
            'brochure.mimes' => 'Brochure must be a PDF file.',
            'brochure.max' => 'Brochure file size should not exceed 10MB.',
            'video_url.url' => 'Please provide a valid video URL (YouTube or Vimeo).',
            'social_media_links.facebook.url' => 'Please provide a valid Facebook URL.',
            'social_media_links.linkedin.url' => 'Please provide a valid LinkedIn URL.',
            'social_media_links.instagram.url' => 'Please provide a valid Instagram URL.',
            'social_media_links.youtube.url' => 'Please provide a valid YouTube URL.',
            'facia_name.required' => 'Please provide the name for your booth fascia board.',
            'additional_details.max' => 'Additional details should not exceed 1000 characters.',
        ]);

        // Handle file uploads
        $logoPath = $this->logo ? $this->logo->store('exhibitors/logos', 'public') : null;
        $brochurePath = $this->brochure ? $this->brochure->store('exhibitors/brochures', 'public') : null;

        // Filter empty social media links
        $socialMediaLinks = array_filter($this->social_media_links, fn($value) => ! empty($value));

        // Create exhibitor
        $exhibitor = Exhibitor::create([
            'brand_name' => $validated['brand_name'],
            'office_address' => $validated['office_address'],
            'city' => $validated['city'],
            'gst_number' => $validated['gst_number'] ?? null,
            'pan_number' => $validated['pan_number'] ?? null,
            'contact_person_name' => $validated['contact_person_name'],
            'phone_number' => $validated['phone_number'],
            'email' => $validated['email'] ?? null,
            'website' => $validated['website'] ?? null,
            'logo_path' => $logoPath,
            'brochure_path' => $brochurePath,
            'video_url' => $validated['video_url'] ?? null,
            'social_media_links' => ! empty($socialMediaLinks) ? $socialMediaLinks : null,
            'facia_name' => $validated['facia_name'] ?? $this->facia_name,
            'additional_details' => $validated['additional_details'] ?? null,
            'stall_type' => $this->stall_type ?: null,
            'stall_number' => $this->stall_number ?: null,
            'stall_size' => $this->stall_size ?: null,
            'total_payment' => $this->total_payment ?: null,
            'payment_received' => $this->payment_received ?: null,
            'payment_pending' => $this->payment_pending ?: null,
            'extra_furniture_details' => $this->extra_furniture_details ?: null,
            'exhibitor_passes_details' => $this->exhibitor_passes_details ?: null,
            'momento_name' => $this->momento_name ?: null,
            'car_pass_details' => $this->car_pass_details ?: null,
        ]);

        // Create projects
        if (! empty($this->projects)) {
            foreach ($this->projects as $projectData) {
                if (! empty($projectData['name'])) {
                    $exhibitor->projects()->create([
                        'name' => $projectData['name'],
                        'area' => $projectData['area'] ?? null,
                        'category' => $projectData['category'] ?? null,
                        'sq_ft' => $projectData['sq_ft'] ?? null,
                        'budget_range' => $projectData['budget_range'] ?? null,
                        'handover_date' => $projectData['handover_date'] ?? null,
                        'status' => $projectData['status'] ?? null,
                        'video_url' => $projectData['video_url'] ?? null,
                        'usp' => $projectData['usp'] ?? null,
                        'contact_person' => $projectData['contact_person'] ?? null,
                    ]);
                }
            }
        }

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
