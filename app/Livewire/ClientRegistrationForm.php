<?php

namespace App\Livewire;

use App\Models\Company;
use App\Models\DraftExhibitor;
use App\Models\Exhibitor;
use Flux\Flux;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.public')]
class ClientRegistrationForm extends Component
{
    use WithFileUploads;

    // Company & Token
    public string $token;

    public ?Company $company = null;

    // Step tracking
    public int $currentStep = 1;

    public array $completedSteps = [];

    // Draft functionality
    public ?int $draftId = null;

    public array $cities = [
        'Surat',
        'Navsari',
        'Ahmedabad',
        'Baroda',
        'Others',
    ];

    // Company Details (fillable by client)
    public string $office_address = '';

    public string $city = '';

    public string $gst_number = '';

    public string $pan_number = '';

    public string $email = '';

    public string $website = '';

    // Branding & Media
    public $logo;

    public $brochure;

    public ?string $logo_path = null;

    public ?string $brochure_path = null;

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

    public bool $use_brand_name_as_momento = false;

    public string $extra_furniture_details = '';

    public string $exhibitor_passes_details = '';

    public string $momento_name = '';

    public string $car_pass_details = '';

    // Projects
    public array $projects = [];

    public ?int $selectedProjectIndex = null;

    // Track which steps have errors
    public array $stepsWithErrors = [];

    /**
     * Component initialization
     */
    public function mount(string $token): void
    {
        // Load company by token
        $this->token = $token;
        $this->company = Company::where('registration_token', $token)->first();

        // Check if company exists
        if (! $this->company) {
            abort(404, 'Invalid registration link');
        }

        // Check if registration is locked
        if ($this->company->is_locked) {
            abort(403, 'This registration link has been locked by the administrator. Please contact support for assistance.');
        }

        // Load existing exhibitor data if already submitted (allow edits)
        if ($this->company->has_submitted) {
            $this->loadExistingExhibitor();
        } else {
            // Load existing draft if it exists
            $this->loadDraft();
        }
    }

    /**
     * Load existing exhibitor data for editing
     */
    protected function loadExistingExhibitor(): void
    {
        $exhibitor = Exhibitor::where('company_id', $this->company->id)->first();

        if ($exhibitor) {
            $this->office_address = $exhibitor->office_address ?? '';
            $this->city = $exhibitor->city ?? '';
            $this->gst_number = $exhibitor->gst_number ?? '';
            $this->logo_path = $exhibitor->logo_path;
            $this->brochure_path = $exhibitor->brochure_path;
            $this->pan_number = $exhibitor->pan_number ?? '';
            $this->email = $exhibitor->email ?? '';
            $this->website = $exhibitor->website ?? '';
            $this->video_url = $exhibitor->video_url ?? '';
            $this->social_media_links = $exhibitor->social_media_links ?? [
                'facebook' => '',
                'linkedin' => '',
                'instagram' => '',
                'youtube' => '',
            ];
            $this->facia_name = $exhibitor->facia_name ?? '';
            $this->additional_details = $exhibitor->additional_details ?? '';
            $this->extra_furniture_details = $exhibitor->extra_furniture_details ?? '';
            $this->exhibitor_passes_details = $exhibitor->exhibitor_passes_details ?? '';
            $this->momento_name = $exhibitor->momento_name ?? '';
            $this->car_pass_details = $exhibitor->car_pass_details ?? '';

            // Load projects
            foreach ($exhibitor->projects as $project) {
                $this->projects[] = [
                    'id' => $project->id,
                    'name' => $project->name,
                    'area' => $project->area,
                    'category' => $project->category,
                    'sq_ft' => $project->sq_ft,
                    'budget_range' => $project->budget_range,
                    'handover_date' => $project->handover_date,
                    'status' => $project->status,
                    'usp' => $project->usp,
                    'contact_person' => $project->contact_person,
                    'video_url' => $project->video_url,
                    'pdf_path' => $project->pdf_path,
                    'logo_path' => $project->logo_path,
                    'units' => $project->units ?? [],
                ];
            }

            // Select first project if any exist
            if (count($this->projects) > 0) {
                $this->selectedProjectIndex = 0;
            }
        }
    }

    /**
     * Load draft data if it exists
     */
    protected function loadDraft(): void
    {
        $draft = DraftExhibitor::where('company_id', $this->company->id)
            ->where('is_completed', false)
            ->first();

        if ($draft) {
            $this->draftId = $draft->id;
            $this->office_address = $draft->office_address ?? '';
            $this->city = $draft->city ?? '';
            $this->gst_number = $draft->gst_number ?? '';
            $this->pan_number = $draft->pan_number ?? '';
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
            $this->extra_furniture_details = $draft->extra_furniture_details ?? '';
            $this->exhibitor_passes_details = $draft->exhibitor_passes_details ?? '';
            $this->momento_name = $draft->momento_name ?? '';
            $this->car_pass_details = $draft->car_pass_details ?? '';
            $this->currentStep = $draft->current_step ?? 1;
            $this->completedSteps = $draft->completed_steps ?? [];
        }
    }

    /**
     * Ensure a draft exists before saving
     */
    protected function ensureDraftExists(): void
    {
        if (! $this->draftId) {
            $draft = DraftExhibitor::create([
                'company_id' => $this->company->id,
                'last_activity_at' => now(),
            ]);

            $this->draftId = $draft->id;
        }
    }

    /**
     * Auto-save the draft whenever relevant data changes
     */
    public function updated($propertyName): void
    {
        // Skip file uploads and internal properties
        if (in_array($propertyName, ['logo', 'brochure', 'use_brand_name_as_facia', 'use_brand_name_as_momento', 'currentStep', 'completedSteps'])) {
            return;
        }

        $this->saveDraft();
    }

    /**
     * Save the current form state as a draft
     */
    public function saveDraft(): void
    {
        $this->ensureDraftExists();

        DraftExhibitor::where('id', $this->draftId)->update([
            'office_address' => $this->office_address ?: null,
            'city' => $this->city ?: null,
            'gst_number' => $this->gst_number ?: null,
            'pan_number' => $this->pan_number ?: null,
            'email' => $this->email ?: null,
            'website' => $this->website ?: null,
            'video_url' => $this->video_url ?: null,
            'social_media_links' => array_filter($this->social_media_links) ?: null,
            'facia_name' => $this->facia_name ?: null,
            'additional_details' => $this->additional_details ?: null,
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
     * Remove the logo
     */
    public function removeLogo(): void
    {
        if ($this->logo) {
            $this->logo->delete();
            $this->logo = null;
        }

        // Also clear the existing logo path to allow replacement
        if ($this->logo_path) {
            // Delete the existing file from storage
            Storage::disk('public')->delete($this->logo_path);
            $this->logo_path = null;

            // Update the database if exhibitor exists
            $exhibitor = Exhibitor::where('company_id', $this->company->id)->first();
            if ($exhibitor) {
                $exhibitor->update(['logo_path' => null]);
            }
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

        // Also clear the existing brochure path to allow replacement
        if ($this->brochure_path) {
            // Delete the existing file from storage
            Storage::disk('public')->delete($this->brochure_path);
            $this->brochure_path = null;

            // Update the database if exhibitor exists
            $exhibitor = Exhibitor::where('company_id', $this->company->id)->first();
            if ($exhibitor) {
                $exhibitor->update(['brochure_path' => null]);
            }
        }
    }

    /**
     * Update facia name when switch is toggled
     */
    public function updatedUseBrandNameAsFacia($value): void
    {
        if ($value) {
            $this->facia_name = strtoupper($this->company->company_name);
        }
    }

    /**
     * Update momento name when switch is toggled
     */
    public function updatedUseBrandNameAsMomento($value): void
    {
        if ($value) {
            $this->momento_name = $this->company->company_name;
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
     * Check for errors in all steps to highlight them
     */
    protected function checkAllStepsForErrors(): void
    {
        $this->stepsWithErrors = [];

        // Check Step 1
        $step1Errors = 0;
        foreach (['office_address', 'city', 'logo', 'brochure'] as $field) {
            if ($this->getErrorBag()->has($field)) {
                $step1Errors++;
            }
        }
        if ($step1Errors > 0) {
            $this->stepsWithErrors[] = 1;
        }

        // Check Step 2
        $step2Errors = 0;
        foreach (['facia_name'] as $field) {
            if ($this->getErrorBag()->has($field)) {
                $step2Errors++;
            }
        }
        if ($step2Errors > 0) {
            $this->stepsWithErrors[] = 2;
        }

        // Check Step 3 (Projects) - check for any error that starts with 'projects.'
        $errorBag = $this->getErrorBag();
        foreach ($errorBag->keys() as $errorKey) {
            if (str_starts_with($errorKey, 'projects.')) {
                $this->stepsWithErrors[] = 3;
                break;
            }
        }
    }

    /**
     * Validate the current step
     */
    protected function validateCurrentStep(): void
    {
        match ($this->currentStep) {
            1 => $this->validate([
                'office_address' => ['required', 'string', 'max:1000'],
                'city' => ['required', 'string', 'in:Surat,Navsari,Ahmedabad,Baroda,Others'],
                'gst_number' => ['nullable', 'string', 'max:255'],
                'pan_number' => ['nullable', 'string', 'max:255'],
                'email' => ['nullable', 'email', 'max:255'],
                'website' => ['nullable', 'url', 'max:255'],
                'logo' => [$this->logo_path ? 'nullable' : 'required', 'file', 'extensions:png,jpg,jpeg,pdf,cdr', 'max:5120'],
                'brochure' => [$this->brochure_path ? 'nullable' : 'required', 'file', 'mimes:pdf', 'max:51200'],
                'video_url' => ['nullable', 'url', 'max:255'],
                'social_media_links' => ['nullable', 'array'],
                'social_media_links.facebook' => ['nullable', 'url', 'max:255'],
                'social_media_links.linkedin' => ['nullable', 'url', 'max:255'],
                'social_media_links.instagram' => ['nullable', 'url', 'max:255'],
                'social_media_links.youtube' => ['nullable', 'url', 'max:255'],
                'additional_details' => ['nullable', 'string', 'max:1000'],
            ], [
                'office_address.required' => 'Company address is required for CREDAI records.',
                'city.required' => 'Please select your main business location.',
                'city.in' => 'Please select a valid city from the dropdown.',
                'email.email' => 'Please provide a valid email address.',
                'website.url' => 'Please provide a valid website URL.',
                'logo.required' => 'Company logo is required.',
                'logo.mimes' => 'Logo must be a PNG, JPG, PDF, or CDR file.',
                'logo.max' => 'Logo file size should not exceed 5MB.',
                'brochure.required' => 'Company brochure is required.',
                'brochure.mimes' => 'Brochure must be a PDF file.',
                'brochure.max' => 'Brochure file size should not exceed 50MB.',
                'video_url.url' => 'Please provide a valid video URL (YouTube or Vimeo).',
                'additional_details.max' => 'Additional details should not exceed 1000 characters.',
            ]),
            2 => $this->validate([
                'extra_furniture_details' => ['nullable', 'string', 'max:1000'],
                'exhibitor_passes_details' => ['nullable', 'string', 'max:1000'],
                'momento_name' => ['nullable', 'string', 'max:255'],
                'facia_name' => ['required', 'string', 'max:255'],
                'car_pass_details' => ['nullable', 'string', 'max:1000'],
            ], [
                'facia_name.required' => 'Please provide the name for your booth fascia board.',
            ]),
            3 => $this->validate([
                'projects' => ['nullable', 'array'],
            ]),
            default => null,
        };
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
            'units' => [],
        ];

        // Select the newly added project
        $this->selectedProjectIndex = count($this->projects) - 1;
    }

    /**
     * Select a project for editing
     */
    public function selectProject(int $index): void
    {
        $this->selectedProjectIndex = $index;
    }

    /**
     * Remove a project from the list
     */
    public function removeProject(int $index): void
    {
        unset($this->projects[$index]);
        $this->projects = array_values($this->projects);

        // Adjust selected project index
        if ($this->selectedProjectIndex === $index) {
            $this->selectedProjectIndex = count($this->projects) > 0 ? 0 : null;
        } elseif ($this->selectedProjectIndex !== null && $this->selectedProjectIndex > $index) {
            $this->selectedProjectIndex--;
        }
    }

    /**
     * Add a residential unit to a project
     */
    public function addResidentialUnit(int $projectIndex): void
    {
        if (! isset($this->projects[$projectIndex]['units'])) {
            $this->projects[$projectIndex]['units'] = [];
        }

        $this->projects[$projectIndex]['units'][] = [
            'type' => 'residential',
            'bedrooms' => '',
            'budget' => '',
            'area' => '',
        ];
    }

    /**
     * Remove a residential unit from a project
     */
    public function removeResidentialUnit(int $projectIndex, int $unitIndex): void
    {
        if (isset($this->projects[$projectIndex]['units'][$unitIndex])) {
            unset($this->projects[$projectIndex]['units'][$unitIndex]);
            $this->projects[$projectIndex]['units'] = array_values($this->projects[$projectIndex]['units']);
        }
    }

    /**
     * Add a commercial unit to a project
     */
    public function addCommercialUnit(int $projectIndex): void
    {
        if (! isset($this->projects[$projectIndex]['units'])) {
            $this->projects[$projectIndex]['units'] = [];
        }

        $this->projects[$projectIndex]['units'][] = [
            'type' => 'commercial',
            'area' => '',
            'budget' => '',
        ];
    }

    /**
     * Remove a commercial unit from a project
     */
    public function removeCommercialUnit(int $projectIndex, int $unitIndex): void
    {
        if (isset($this->projects[$projectIndex]['units'][$unitIndex])) {
            unset($this->projects[$projectIndex]['units'][$unitIndex]);
            $this->projects[$projectIndex]['units'] = array_values($this->projects[$projectIndex]['units']);
        }
    }

    /**
     * Remove a file from a project (PDF or logo)
     */
    public function removeProjectFile(int $index, string $type): void
    {
        // Remove temporary uploaded file
        if (isset($this->projects[$index][$type])) {
            unset($this->projects[$index][$type]);
        }

        // Also clear the existing file path if it exists
        $pathKey = $type.'_path';
        if (isset($this->projects[$index][$pathKey])) {
            // Delete the existing file from storage
            Storage::disk('public')->delete($this->projects[$index][$pathKey]);
            unset($this->projects[$index][$pathKey]);

            // Update the database if this project already exists
            if (isset($this->projects[$index]['id'])) {
                $project = \App\Models\Project::find($this->projects[$index]['id']);
                if ($project) {
                    $project->update([$pathKey => null]);
                }
            }
        }
    }

    /**
     * Handle new photo upload for a project
     */
    public function updatedProjects($value, $key): void
    {
        // Check if this is a newPhoto update for a specific project
        if (str_contains($key, '.newPhoto')) {
            $projectIndex = (int) explode('.', $key)[0];

            if (! isset($this->projects[$projectIndex]['newPhoto'])) {
                return;
            }

            // Validate the photo
            $this->validate([
                "projects.{$projectIndex}.newPhoto" => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            ], [
                "projects.{$projectIndex}.newPhoto.image" => 'Photo must be an image file.',
                "projects.{$projectIndex}.newPhoto.mimes" => 'Photo must be a PNG or JPG file.',
                "projects.{$projectIndex}.newPhoto.max" => 'Photo file size should not exceed 2MB.',
            ]);

            // Initialize photos array if it doesn't exist
            if (! isset($this->projects[$projectIndex]['photos'])) {
                $this->projects[$projectIndex]['photos'] = [];
            }

            // Check if we haven't exceeded the limit
            if (count($this->projects[$projectIndex]['photos']) >= 5) {
                $this->addError("projects.{$projectIndex}.newPhoto", 'You can upload a maximum of 5 photos.');
                unset($this->projects[$projectIndex]['newPhoto']);

                return;
            }

            // Add to photos array
            $this->projects[$projectIndex]['photos'][] = $this->projects[$projectIndex]['newPhoto'];

            // Clear the newPhoto property
            unset($this->projects[$projectIndex]['newPhoto']);
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
     * Validate project units based on category
     */
    protected function validateProjectUnits(): void
    {
        // Projects are optional, so if empty, no validation needed
        if (empty($this->projects)) {
            return;
        }

        foreach ($this->projects as $index => $project) {
            if (! isset($project['category'])) {
                continue;
            }

            if ($project['category'] === 'Residential') {
                if (! isset($project['units']) || count($project['units']) === 0) {
                    $this->addError("projects.{$index}.units", 'Please add at least one residential unit.');
                }
            } elseif (in_array($project['category'], ['Commercial Office', 'Commercial Shop / Showroom'])) {
                if (! isset($project['units']) || count($project['units']) === 0) {
                    $this->addError("projects.{$index}.units", 'Please add at least one commercial unit.');
                }
            } elseif ($project['category'] === 'Plotting') {
                if (empty($project['sq_ft'])) {
                    $this->addError("projects.{$index}.sq_ft", 'Please enter the area for plotting.');
                }
            }
        }
    }

    /**
     * Submit the exhibitor form
     */
    public function submit(): void
    {
        // Validate project units first (adds errors to error bag without stopping)
        $this->validateProjectUnits();

        // Run main validation - this throws ValidationException if it fails
        try {
            $validated = $this->validate([
                // Company Details
                'office_address' => ['required', 'string', 'max:1000'],
                'city' => ['required', 'string', 'in:Surat,Navsari,Ahmedabad,Baroda,Others'],
                'gst_number' => ['nullable', 'string', 'max:255'],
                'pan_number' => ['nullable', 'string', 'max:255'],
                'email' => ['nullable', 'email', 'max:255'],
                'website' => ['nullable', 'url', 'max:255'],

                // Branding & Media
                'logo' => [$this->logo_path ? 'nullable' : 'required', 'file', 'extensions:png,jpg,jpeg,pdf,cdr', 'max:5120'],
                'brochure' => [$this->brochure_path ? 'nullable' : 'required', 'file', 'mimes:pdf', 'max:51200'],
                'video_url' => ['nullable', 'url', 'max:255'],
                'social_media_links' => ['nullable', 'array'],

                // Exhibition Display
                'facia_name' => ['required', 'string', 'max:255'],
                'additional_details' => ['nullable', 'string', 'max:1000'],
            ], [
                'logo.required' => 'Company logo is required.',
                'brochure.required' => 'Company brochure is required.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation failed - check all steps for errors and highlight them
            $this->checkAllStepsForErrors();

            // Navigate to first step with errors
            if (! empty($this->stepsWithErrors)) {
                $this->currentStep = min($this->stepsWithErrors);
            }

            throw $e;
        }

        // Handle file uploads with original filenames
        $logoPath = $this->logo ? $this->logo->storeAs(
            'exhibitors/logos',
            time().'_'.$this->logo->getClientOriginalName(),
            'public'
        ) : null;
        $brochurePath = $this->brochure ? $this->brochure->storeAs(
            'exhibitors/brochures',
            time().'_'.$this->brochure->getClientOriginalName(),
            'public'
        ) : null;

        // Filter empty social media links
        $socialMediaLinks = array_filter($this->social_media_links, fn ($value) => ! empty($value));

        // Check if exhibitor already exists (for updates)
        $exhibitor = Exhibitor::where('company_id', $this->company->id)->first();

        $exhibitorData = [
            'company_id' => $this->company->id,
            'office_address' => $validated['office_address'],
            'city' => $validated['city'],
            'gst_number' => $validated['gst_number'] ?? null,
            'pan_number' => $validated['pan_number'] ?? null,
            'email' => $validated['email'] ?? null,
            'website' => $validated['website'] ?? null,
            'video_url' => $validated['video_url'] ?? null,
            'social_media_links' => ! empty($socialMediaLinks) ? $socialMediaLinks : null,
            'facia_name' => $validated['facia_name'],
            'additional_details' => $validated['additional_details'] ?? null,
            'extra_furniture_details' => $this->extra_furniture_details ?: null,
            'exhibitor_passes_details' => $this->exhibitor_passes_details ?: null,
            'momento_name' => $this->momento_name ?: null,
            'car_pass_details' => $this->car_pass_details ?: null,
        ];

        // Only update file paths if new files were uploaded
        if ($logoPath) {
            $exhibitorData['logo_path'] = $logoPath;
        }
        if ($brochurePath) {
            $exhibitorData['brochure_path'] = $brochurePath;
        }

        if ($exhibitor) {
            // Update existing exhibitor
            $exhibitor->update($exhibitorData);
        } else {
            // Create new exhibitor
            $exhibitor = Exhibitor::create($exhibitorData);
        }

        // Sync projects (delete removed ones, update existing, create new)
        $existingProjectIds = [];
        if (! empty($this->projects)) {
            foreach ($this->projects as $projectData) {
                if (! empty($projectData['name'])) {
                    // Handle project file uploads
                    $projectPdfPath = null;
                    $projectLogoPath = null;

                    if (isset($projectData['pdf']) && is_object($projectData['pdf'])) {
                        $projectPdfPath = $projectData['pdf']->storeAs(
                            'projects/pdfs',
                            time().'_'.$projectData['pdf']->getClientOriginalName(),
                            'public'
                        );
                    }

                    if (isset($projectData['logo']) && is_object($projectData['logo'])) {
                        $projectLogoPath = $projectData['logo']->storeAs(
                            'projects/logos',
                            time().'_'.$projectData['logo']->getClientOriginalName(),
                            'public'
                        );
                    }

                    $projectUpdateData = [
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
                        'units' => $projectData['units'] ?? null,
                    ];

                    // Only update file paths if new files were uploaded
                    if ($projectPdfPath) {
                        $projectUpdateData['pdf_path'] = $projectPdfPath;
                    }
                    if ($projectLogoPath) {
                        $projectUpdateData['logo_path'] = $projectLogoPath;
                    }

                    if (isset($projectData['id'])) {
                        // Update existing project
                        $project = $exhibitor->projects()->find($projectData['id']);
                        if ($project) {
                            $project->update($projectUpdateData);
                            $existingProjectIds[] = $project->id;
                        }
                    } else {
                        // Create new project
                        $project = $exhibitor->projects()->create($projectUpdateData);
                        $existingProjectIds[] = $project->id;
                    }
                }
            }
        }

        // Delete projects that were removed
        $exhibitor->projects()->whereNotIn('id', $existingProjectIds)->delete();

        // Mark company as submitted
        $this->company->markAsSubmitted();

        // Mark draft as completed
        if ($this->draftId) {
            DraftExhibitor::where('id', $this->draftId)->update([
                'is_completed' => true,
            ]);
        }

        // Mark company as submitted
        $this->company->markAsSubmitted();

        // Show thank you modal using Flux
        Flux::modal('thank-you')->show();
    }

    /**
     * Close the thank you modal and reload
     */
    public function closeThankYouModal(): void
    {
        Flux::modal('thank-you')->close();
        $this->redirect(route('client.register', ['token' => $this->token]), navigate: true);
    }

    #[Title('Exhibitor Registration - CREDAI')]
    public function render()
    {
        return view('livewire.client-registration-form');
    }
}
