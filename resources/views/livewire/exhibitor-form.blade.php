<div class="mx-auto w-full max-w-4xl">
    <div class="mb-8">
        <flux:heading size="xl" class="mb-2">Exhibitor Information Form</flux:heading>
        <flux:subheading>Please fill in all the required details for your exhibition booth</flux:subheading>
    </div>

    @if (session('success'))
        <flux:callout variant="success" class="mb-6">
            {{ session('success') }}
        </flux:callout>
    @endif

    @if (session('info'))
        <flux:callout variant="info" class="mb-6">
            {{ session('info') }}
        </flux:callout>
    @endif

    @if (session('warning'))
        <flux:callout variant="warning" class="mb-6">
            {{ session('warning') }}
        </flux:callout>
    @endif

    <!-- Progress Indicator -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            @for ($i = 1; $i <= 4; $i++)
                <button type="button" wire:click="goToStep({{ $i }})"
                    class="{{ implode(
                        ' ',
                        array_filter([
                            'flex shrink-0 items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition',
                            $currentStep === $i ? 'bg-zinc-100 text-zinc-900 dark:bg-zinc-800 dark:text-white' : '',
                            in_array($i, $completedSteps) && $currentStep !== $i ? 'text-green-600 dark:text-green-500' : '',
                            !in_array($i, $completedSteps) && $currentStep !== $i ? 'text-zinc-400 dark:text-zinc-600' : '',
                        ]),
                    ) }}">
                    <span
                        class="{{ implode(
                            ' ',
                            array_filter([
                                'flex h-8 w-8 shrink-0 items-center justify-center rounded-full md:h-6 md:w-6',
                                $currentStep === $i ? 'bg-zinc-600 text-white dark:bg-zinc-400 dark:text-zinc-900' : '',
                                in_array($i, $completedSteps) && $currentStep !== $i ? 'bg-green-600 text-white dark:bg-green-500' : '',
                                !in_array($i, $completedSteps) && $currentStep !== $i
                                    ? 'bg-zinc-200 text-zinc-400 dark:bg-zinc-800 dark:text-zinc-600'
                                    : '',
                            ]),
                        ) }}">
                        @if (in_array($i, $completedSteps))
                            <flux:icon.check variant="outline" class="size-4 md:size-3.5" />
                        @else
                            <!-- Mobile: Show number -->
                            <span class="text-xs font-semibold md:hidden">{{ $i }}</span>
                            <!-- Desktop: Show icon -->
                            <span class="hidden md:inline">
                                @if ($i === 1)
                                    <flux:icon.building-office-2 class="size-3.5" />
                                @elseif ($i === 2)
                                    <flux:icon.user class="size-3.5" />
                                @elseif ($i === 3)
                                    <flux:icon.photo class="size-3.5" />
                                @elseif ($i === 4)
                                    <flux:icon.sparkles class="size-3.5" />
                                @endif
                            </span>
                        @endif
                    </span>
                    <span class="hidden whitespace-nowrap md:inline">
                        @if ($i === 1)
                            Company Details
                        @elseif ($i === 2)
                            Contact Person
                        @elseif ($i === 3)
                            Branding & Media
                        @elseif ($i === 4)
                            Exhibition Display
                        @endif
                    </span>
                </button>

                @if ($i < 4)
                    <div
                        class="{{ implode(
                            ' ',
                            array_filter([
                                'h-0.5 flex-1',
                                in_array($i, $completedSteps) ? 'bg-green-600 dark:bg-green-500' : 'bg-zinc-300 dark:bg-zinc-700',
                            ]),
                        ) }}">
                    </div>
                @endif
            @endfor
        </div>
    </div>

    <!-- Current Step Header (Mobile Only - Sticky) -->
    <div class="sticky top-0 z-30 mb-6 bg-white pb-3 dark:bg-zinc-950 md:hidden">
        <div
            class="rounded-lg border border-zinc-200 bg-gradient-to-r from-zinc-50 to-white p-4 dark:border-zinc-700 dark:from-zinc-900 dark:to-zinc-800">
            <div class="flex items-center gap-3">
                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-zinc-600 text-white dark:bg-zinc-400 dark:text-zinc-900">
                    @if ($currentStep === 1)
                        <flux:icon.building-office-2 class="size-5" />
                    @elseif ($currentStep === 2)
                        <flux:icon.user class="size-5" />
                    @elseif ($currentStep === 3)
                        <flux:icon.photo class="size-5" />
                    @elseif ($currentStep === 4)
                        <flux:icon.sparkles class="size-5" />
                    @endif
                </div>
                <div>
                    <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Step {{ $currentStep }} of 4
                    </div>
                    <div class="text-base font-semibold text-zinc-900 dark:text-white">
                        @if ($currentStep === 1)
                            Company Details
                        @elseif ($currentStep === 2)
                            Contact Person
                        @elseif ($currentStep === 3)
                            Branding & Media
                        @elseif ($currentStep === 4)
                            Exhibition Display
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form wire:submit="submit" class="space-y-6">
        <!-- Step 1: Company Details -->
        @if ($currentStep === 1)
            <flux:card>
                <div class="space-y-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <flux:input wire:model="brand_name" label="Company / Brand Name" placeholder="ABC Developers"
                            description="Name shown on your booth and website." required />

                        <flux:select wire:model="city" label="City" placeholder="Select city"
                            description="Your main office location" variant="listbox" searchable required>
                            <flux:select.option value="Surat">Surat</flux:select.option>
                            <flux:select.option value="Navsari">Navsari</flux:select.option>
                            <flux:select.option value="Ahmedabad">Ahmedabad</flux:select.option>
                            <flux:select.option value="Baroda">Baroda</flux:select.option>
                            <flux:select.option value="Others">Others</flux:select.option>
                        </flux:select>
                    </div>

                    <flux:textarea wire:model="office_address" label="Company Address"
                        placeholder="123 Main Street, Building Name, Area"
                        description="Shown to visitors on your exhibitor page and in location search." rows="3"
                        required />

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <flux:input wire:model="gst_number" label="GST Number" placeholder="22AAAAA0000A1Z5"
                            description="Your company's GST registration number." badge="Optional" />

                        <flux:input wire:model="pan_number" label="PAN Card" placeholder="AAAAA0000A"
                            description="Your company's PAN card number." badge="Optional" />
                    </div>
                </div>
            </flux:card>
        @endif

        <!-- Step 2: Contact Person -->
        @if ($currentStep === 2)
            <flux:card>
                <div class="space-y-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <flux:input wire:model="contact_person_name" label="Main Contact Person" placeholder="John Doe"
                            description="Person handling event coordination." required />

                        <flux:input wire:model="phone_number" label="Mobile Number" type="tel" inputmode="numeric"
                            placeholder="9876543210" description="Enter 10 digit mobile number — WhatsApp preferred."
                            required />
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <flux:input wire:model="email" label="Email ID" type="email" placeholder="contact@example.com"
                            description="For visitor enquiries and official updates." badge="Optional" />

                        <flux:input wire:model="website" label="Website URL" type="url"
                            placeholder="https://www.example.com" description="Link to your official company website."
                            badge="Optional" />
                    </div>
                </div>
            </flux:card>
        @endif

        <!-- Step 3: Branding & Media -->
        @if ($currentStep === 3)
            <flux:card>
                <div class="space-y-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <!-- Logo Upload -->
                        <div x-data="{
                            uploading: false,
                            isDragging: false,
                            handleFile(event) {
                                const file = event.target.files[0];
                                if (file) {
                                    this.uploading = true;
                                    @this.upload('logo', file, () => {
                                        this.uploading = false;
                                    }, () => {
                                        this.uploading = false;
                                    })
                                }
                            },
                            handleDrop(event) {
                                event.preventDefault();
                                this.isDragging = false;
                                const file = event.dataTransfer.files[0];
                                if (file && (file.type.match(/^image\/(png|jpeg|jpg)$/) || file.type === 'application/pdf' || file.name.endsWith('.cdr'))) {
                                    this.uploading = true;
                                    @this.upload('logo', file, () => {
                                        this.uploading = false;
                                    }, () => {
                                        this.uploading = false;
                                    })
                                }
                            }
                        }">
                            <div class="space-y-2">
                                <flux:label>Upload Logo</flux:label>
                                <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">
                                    PNG, JPG, PDF, or CDR format accepted.
                                </flux:text>

                                <div class="relative">
                                    <input type="file" @change="handleFile($event)"
                                        accept="image/png,image/jpeg,image/jpg,application/pdf,.cdr,application/x-coreldraw,application/coreldraw"
                                        class="hidden" id="logo-upload"
                                        :disabled="uploading || {{ $logo ? 'true' : 'false' }}" />
                                    <label for="logo-upload" @dragover.prevent="isDragging = true"
                                        @dragleave.prevent="isDragging = false" @drop.prevent="handleDrop($event)"
                                        class="{{ implode(' ', [
                                            'flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed',
                                            'border-zinc-300 bg-zinc-50 px-6 py-8 transition hover:border-zinc-400',
                                            'dark:border-zinc-700 dark:bg-zinc-900 dark:hover:border-zinc-600',
                                        ]) }}"
                                        :class="{
                                            'cursor-not-allowed opacity-60': uploading ||
                                                {{ $logo ? 'true' : 'false' }},
                                            'border-blue-400 bg-blue-50 dark:border-blue-500 dark:bg-blue-900/20': isDragging
                                        }">
                                        <flux:icon.photo class="mb-3 size-10 text-zinc-400 dark:text-zinc-600" />
                                        <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300"
                                            x-show="!uploading">
                                            Drop file or click to browse
                                        </span>
                                        <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300"
                                            x-show="uploading" x-cloak>
                                            Uploading...
                                        </span>
                                        <span class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                            PNG, JPG, PDF, or CDR, max 5MB
                                        </span>
                                    </label>
                                </div>

                                <div x-show="uploading" x-cloak
                                    class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                                    <svg class="size-4 animate-spin" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    <span>Processing upload...</span>
                                </div>
                            </div>

                            @if ($logo)
                                <div class="mt-3">
                                    <flux:file-item :heading="$logo->getClientOriginalName()"
                                        :image="$logo->temporaryUrl()" :size="$logo->getSize()">
                                        <x-slot name="actions">
                                            <flux:file-item.remove wire:click="removeLogo" aria-label="Remove logo" />
                                        </x-slot>
                                    </flux:file-item>
                                </div>
                            @endif

                            @error('logo')
                                <flux:text class="text-sm text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                            @enderror
                        </div>

                        <!-- Brochure Upload -->
                        <div x-data="{
                            uploading: false,
                            isDragging: false,
                            handleFile(event) {
                                const file = event.target.files[0];
                                if (file) {
                                    this.uploading = true;
                                    @this.upload('brochure', file, () => {
                                        this.uploading = false;
                                    }, () => {
                                        this.uploading = false;
                                    })
                                }
                            },
                            handleDrop(event) {
                                event.preventDefault();
                                this.isDragging = false;
                                const file = event.dataTransfer.files[0];
                                if (file && file.type === 'application/pdf') {
                                    this.uploading = true;
                                    @this.upload('brochure', file, () => {
                                        this.uploading = false;
                                    }, () => {
                                        this.uploading = false;
                                    })
                                }
                            }
                        }">
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <flux:label>Company Brochure</flux:label>
                                    <flux:badge size="sm" variant="outline" color="zinc" inset="top bottom">
                                        Optional</flux:badge>
                                </div>
                                <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">
                                    Upload a PDF of your company profile.
                                </flux:text>

                                <div class="relative">
                                    <input type="file" @change="handleFile($event)" accept="application/pdf"
                                        class="hidden" id="brochure-upload"
                                        :disabled="uploading || {{ $brochure ? 'true' : 'false' }}" />
                                    <label for="brochure-upload" @dragover.prevent="isDragging = true"
                                        @dragleave.prevent="isDragging = false" @drop.prevent="handleDrop($event)"
                                        class="{{ implode(' ', [
                                            'flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed',
                                            'border-zinc-300 bg-zinc-50 px-6 py-8 transition hover:border-zinc-400',
                                            'dark:border-zinc-700 dark:bg-zinc-900 dark:hover:border-zinc-600',
                                        ]) }}"
                                        :class="{
                                            'cursor-not-allowed opacity-60': uploading ||
                                                {{ $brochure ? 'true' : 'false' }},
                                            'border-blue-400 bg-blue-50 dark:border-blue-500 dark:bg-blue-900/20': isDragging
                                        }">
                                        <flux:icon.document-text
                                            class="mb-3 size-10 text-zinc-400 dark:text-zinc-600" />
                                        <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300"
                                            x-show="!uploading">
                                            Drop file or click to browse
                                        </span>
                                        <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300"
                                            x-show="uploading" x-cloak>
                                            Uploading...
                                        </span>
                                        <span class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                            PDF, max 10MB
                                        </span>
                                    </label>
                                </div>

                                <div x-show="uploading" x-cloak
                                    class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                                    <svg class="size-4 animate-spin" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    <span>Processing upload...</span>
                                </div>
                            </div>

                            @if ($brochure)
                                <div class="mt-3">
                                    <flux:file-item :heading="$brochure->getClientOriginalName()"
                                        :size="$brochure->getSize()" icon="document-text">
                                        <x-slot name="actions">
                                            <flux:file-item.remove wire:click="removeBrochure"
                                                aria-label="Remove brochure" />
                                        </x-slot>
                                    </flux:file-item>
                                </div>
                            @endif

                            @error('brochure')
                                <flux:text class="text-sm text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                            @enderror
                        </div>
                    </div>

                    <!-- Photos Upload -->
                    <div x-data="{
                        uploading: false,
                        isDragging: false,
                        uploadQueue: [],
                        isProcessing: false,
                        async processQueue() {
                            if (this.uploadQueue.length > 0 && !this.isProcessing) {
                                this.isProcessing = true;
                                this.uploading = true;
                                const file = this.uploadQueue.shift();
                                console.log('Starting upload for:', file.name, 'Queue remaining:', this.uploadQueue.length);

                                try {
                                    await new Promise((resolve, reject) => {
                                        @this.upload('newPhoto', file, resolve, reject);
                                    });
                                    console.log('Upload complete:', file.name);
                                } catch (error) {
                                    console.error('Upload failed:', error);
                                }

                                this.isProcessing = false;

                                // Continue with next file
                                if (this.uploadQueue.length > 0) {
                                    setTimeout(() => this.processQueue(), 100);
                                } else {
                                    // All done, hide loading state
                                    this.uploading = false;
                                }
                            }
                        },
                        handleFiles(event) {
                            const files = Array.from(event.target.files);
                            const validFiles = files.filter(file =>
                                file.type.match(/^image\/(png|jpeg|jpg)$/)
                            );
                            console.log('Adding files to queue:', validFiles.length, validFiles.map(f => f.name));
                            this.uploadQueue.push(...validFiles);
                            event.target.value = '';
                            this.processQueue();
                        },
                        handleDrop(event) {
                            event.preventDefault();
                            this.isDragging = false;
                            const files = Array.from(event.dataTransfer.files);
                            const validFiles = files.filter(file =>
                                file.type.match(/^image\/(png|jpeg|jpg)$/)
                            );
                            console.log('Dropping files:', validFiles.length, validFiles.map(f => f.name));
                            if (validFiles.length > 0) {
                                this.uploadQueue.push(...validFiles);
                                this.processQueue();
                            }
                        }
                    }" x-init="console.log('Photo upload component initialized');
                    $watch('uploadQueue', value => console.log('Queue updated:', value.length))">
                        <div class="space-y-2">
                            <flux:label>Upload Images</flux:label>
                            <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">
                                Upload 3–5 photos that best showcase your company or projects. Minimum 3 required.
                            </flux:text>

                            @if (count($photos) < 5)
                                <!-- Custom file input with better UX -->
                                <div class="relative">
                                    <div wire:ignore>
                                        <input type="file" @change="handleFiles($event)"
                                            accept="image/png,image/jpeg,image/jpg" multiple class="hidden"
                                            id="photo-upload" :disabled="uploading || {{ count($photos) }} >= 5" />
                                    </div>
                                    <label for="photo-upload" @dragover.prevent="isDragging = true"
                                        @dragleave.prevent="isDragging = false" @drop.prevent="handleDrop($event)"
                                        class="{{ implode(' ', [
                                            'flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed',
                                            'border-zinc-300 bg-zinc-50 px-6 py-8 transition hover:border-zinc-400',
                                            'dark:border-zinc-700 dark:bg-zinc-900 dark:hover:border-zinc-600',
                                        ]) }}"
                                        :class="{
                                            'cursor-not-allowed opacity-60': uploading,
                                            'border-blue-400 bg-blue-50 dark:border-blue-500 dark:bg-blue-900/20': isDragging
                                        }">
                                        <flux:icon.photo class="mb-3 size-10 text-zinc-400 dark:text-zinc-600" />
                                        <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300"
                                            x-show="!uploading">
                                            Drop files or click to browse
                                        </span>
                                        <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300"
                                            x-show="uploading" x-cloak>
                                            Uploading...
                                        </span>
                                        <span class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                            PNG or JPG, 3–5 photos required, 2MB each
                                        </span>
                                        <span class="mt-2 text-xs font-medium text-zinc-600 dark:text-zinc-400">
                                            Uploaded: {{ count($photos) }}/5
                                        </span>
                                    </label>
                                </div>

                                <!-- Loading state -->
                                <div x-show="uploading" x-cloak
                                    class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                                    <svg class="size-4 animate-spin" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    <span>Processing upload... (<span x-text="uploadQueue.length"></span>
                                        remaining)</span>
                                </div>
                            @else
                                <div class="rounded-lg bg-green-50 px-4 py-3 dark:bg-green-900/20">
                                    <div class="flex items-center gap-2 text-sm text-green-700 dark:text-green-400">
                                        <flux:icon.check class="size-5" />
                                        <span class="font-medium">Maximum photos uploaded (5/5)</span>
                                    </div>
                                    <p class="mt-1 text-xs text-green-600 dark:text-green-500">
                                        Remove a photo below to upload a different one.
                                    </p>
                                </div>
                            @endif
                        </div>

                        @if (count($photos) > 0)
                            <div class="mt-4 space-y-3" wire:key="photos-list">
                                @foreach ($photos as $index => $photo)
                                    <div wire:key="photo-{{ $index }}-{{ $photo->getClientOriginalName() }}">
                                        <flux:file-item :heading="$photo->getClientOriginalName()"
                                            :image="$photo->temporaryUrl()" :size="$photo->getSize()">
                                            <x-slot name="actions">
                                                <flux:file-item.remove wire:click="removePhoto({{ $index }})"
                                                    aria-label="Remove photo {{ $index + 1 }}" />
                                            </x-slot>
                                        </flux:file-item>
                                        <div class="mt-2">
                                            <flux:input wire:model.defer="photo_labels.{{ $index }}"
                                                placeholder="e.g., Showroom Interior, Project Front View"
                                                badge="Optional" />
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @error('photos')
                            <flux:text class="text-sm text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                        @enderror
                    </div>

                    <flux:input wire:model="video_url" label="Promotional Video URL" type="url"
                        placeholder="https://youtube.com/watch?v=..."
                        description="YouTube or Vimeo link to feature on your exhibitor page." badge="Optional" />

                    <div>
                        <div class="mb-3 flex items-center gap-2">
                            <flux:heading size="base">Social Media Links</flux:heading>
                            <flux:badge size="sm" variant="outline" color="zinc">Optional</flux:badge>
                        </div>
                        <flux:text class="mb-4 text-sm text-zinc-600 dark:text-zinc-400">Add links to help visitors
                            verify and follow your brand.</flux:text>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <flux:input wire:model="social_media_links.facebook" label="Facebook" type="url"
                                placeholder="https://facebook.com/yourcompany" badge="Optional">
                                <x-slot name="iconLeading">
                                    <x-bi-facebook />
                                </x-slot>
                            </flux:input>

                            <flux:input wire:model="social_media_links.instagram" label="Instagram" type="url"
                                placeholder="https://instagram.com/yourcompany" badge="Optional">
                                <x-slot name="iconLeading">
                                    <x-bi-instagram />
                                </x-slot>
                            </flux:input>

                            <flux:input wire:model="social_media_links.linkedin" label="LinkedIn" type="url"
                                placeholder="https://linkedin.com/company/yourcompany" badge="Optional">
                                <x-slot name="iconLeading">
                                    <x-bi-linkedin />
                                </x-slot>
                            </flux:input>

                            <flux:input wire:model="social_media_links.youtube" label="YouTube" type="url"
                                placeholder="https://youtube.com/@yourcompany" badge="Optional">
                                <x-slot name="iconLeading">
                                    <x-bi-youtube />
                                </x-slot>
                            </flux:input>
                        </div>
                    </div>
                </div>
            </flux:card>
        @endif

        <!-- Step 4: Exhibition Display -->
        @if ($currentStep === 4)
            <flux:card>
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <flux:label>Use Company Name as Facia Name</flux:label>
                        <flux:switch wire:model.live="use_brand_name_as_facia" />
                    </div>

                    <flux:input wire:model="facia_name" label="Facia Name (Booth Header Text)"
                        placeholder="ABC DEVELOPERS"
                        description="The name that should appear on your booth fascia board."
                        :disabled="$use_brand_name_as_facia" required />

                    <flux:textarea wire:model="additional_details" label="Additional Details"
                        placeholder="e.g., Established in 2010, Specializing in luxury villas..."
                        description="Add any additional information like establishment year, specializations, awards, etc."
                        rows="4" badge="Optional" />
                </div>
            </flux:card>
        @endif

        <!-- Navigation Buttons -->
        <div class="flex justify-between gap-4">
            <div>
                @if ($currentStep > 1)
                    <flux:button type="button" variant="ghost" icon="arrow-left" wire:click="previousStep">
                        Previous
                    </flux:button>
                @elseif (!$this->isPublicForm())
                    <flux:button variant="ghost" href="{{ route('dashboard') }}" wire:navigate>
                        Cancel
                    </flux:button>
                @endif
            </div>

            <div>
                @if ($currentStep < 4)
                    <flux:button type="button" variant="primary" icon:trailing="arrow-right" wire:click="nextStep"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="nextStep">Next</span>
                        <span wire:loading wire:target="nextStep">Validating...</span>
                    </flux:button>
                @else
                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="submit">Submit Application</span>
                        <span wire:loading wire:target="submit">Submitting...</span>
                    </flux:button>
                @endif
            </div>
        </div>
    </form>

    <!-- Floating Save Progress Button (only show when draft exists) -->
    @if ($showResumeLink && $resumeToken)
        <div x-data="{
            expanded: false,
            showModal: false,
            isMobile: window.innerWidth < 768,
            copyLink() {
                navigator.clipboard.writeText('{{ $this->resumeUrl }}');
                this.$dispatch('link-copied');
                if (this.isMobile) {
                    setTimeout(() => this.showModal = false, 1500);
                }
            }
        }" x-init="// Update isMobile on resize
        window.addEventListener('resize', () => {
            isMobile = window.innerWidth < 768;
            if (!isMobile) showModal = false;
        });">
            <!-- Mobile: Floating button at bottom center -->
            <div class="md:hidden">
                <!-- Floating Button -->
                <div class="fixed bottom-6 left-1/2 z-40 -translate-x-1/2">
                    <flux:button @click="showModal = true" type="button" variant="primary" icon="bookmark"
                        color="emerald" class="shadow-lg">
                        Save Progress
                    </flux:button>
                </div>

                <!-- Mobile Modal -->
                <div x-show="showModal" x-cloak @click.self="showModal = false"
                    class="fixed inset-0 z-50 flex items-end justify-center bg-black/50 p-4">
                    <div @click.away="showModal = false" x-show="showModal"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="translate-y-full opacity-0"
                        x-transition:enter-end="translate-y-0 opacity-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="translate-y-0 opacity-100"
                        x-transition:leave-end="translate-y-full opacity-0"
                        class="w-full max-w-lg rounded-t-2xl bg-white p-6 dark:bg-zinc-800">
                        <div class="mb-4 flex items-start justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Save Your Progress</h3>
                                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Your form is auto-saved</p>
                            </div>
                            <flux:button @click="showModal = false" type="button" variant="ghost" icon="x-mark"
                                square size="sm" />
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Resume
                                    Link</label>
                                <div class="flex gap-2">
                                    <input type="text" readonly value="{{ $this->resumeUrl }}"
                                        class="flex-1 rounded-lg border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm font-mono text-zinc-900 dark:border-zinc-600 dark:bg-zinc-900 dark:text-zinc-100">
                                    <flux:button @click="copyLink" type="button" variant="primary" size="sm">
                                        Copy
                                    </flux:button>
                                </div>
                            </div>

                            <div
                                class="rounded-lg border border-amber-200 bg-amber-50 p-3 dark:border-amber-900/50 dark:bg-amber-900/20">
                                <p class="text-sm text-amber-800 dark:text-amber-200">
                                    <strong>Note:</strong> Text data is saved automatically. Images will not be saved in
                                    drafts.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Desktop: Collapsible widget in bottom-right corner -->
            <div class="hidden md:block">
                <!-- Collapsed state: Just a small button -->
                <div x-show="!expanded" class="fixed bottom-6 right-6 z-40 transition-all duration-200">
                    <flux:button @click="expanded = true" type="button" variant="primary" color="emerald"
                        icon="bookmark" class="shadow-lg">
                        Save Progress
                    </flux:button>
                </div>

                <!-- Expanded state: Show details -->
                <div x-show="expanded" x-cloak x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="translate-y-4 opacity-0"
                    x-transition:enter-end="translate-y-0 opacity-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="translate-y-0 opacity-100"
                    x-transition:leave-end="translate-y-4 opacity-0"
                    class="fixed bottom-6 right-6 z-40 w-96 rounded-lg border border-zinc-200 bg-white p-4 shadow-xl dark:border-zinc-700 dark:bg-zinc-800">
                    <div class="mb-3 flex items-start justify-between">
                        <div>
                            <h3 class="font-semibold text-zinc-900 dark:text-white">Save Your Progress</h3>
                            <p class="mt-1 text-xs text-zinc-600 dark:text-zinc-400">Auto-saved</p>
                        </div>
                        <flux:button @click="expanded = false" type="button" variant="ghost" icon="x-mark" square
                            size="xs" inset />
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-zinc-700 dark:text-zinc-300">Resume
                                Link</label>
                            <div class="flex gap-2">
                                <input type="text" readonly value="{{ $this->resumeUrl }}"
                                    class="flex-1 rounded border border-zinc-300 bg-zinc-50 px-2.5 py-1.5 text-xs font-mono text-zinc-900 dark:border-zinc-600 dark:bg-zinc-900 dark:text-zinc-100">
                                <flux:button @click="copyLink" type="button" variant="primary" size="xs">
                                    Copy
                                </flux:button>
                            </div>
                        </div>

                        <div
                            class="rounded border border-amber-200 bg-amber-50 p-2.5 dark:border-amber-900/50 dark:bg-amber-900/20">
                            <p class="text-xs text-amber-800 dark:text-amber-200">
                                <strong>Note:</strong> Text data is saved. Images are not saved in drafts.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Toast notification for copy success -->
            <div x-data="{ show: false }" @link-copied.window="show = true; setTimeout(() => show = false, 2000)"
                x-show="show" x-cloak x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="translate-y-2 opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="translate-y-0 opacity-100" x-transition:leave-end="translate-y-2 opacity-0"
                class="fixed bottom-20 right-6 z-50 flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm text-white shadow-lg dark:bg-green-500">
                <flux:icon.check class="size-5" />
                <span>Link copied!</span>
            </div>
        </div>
    @endif
</div>
