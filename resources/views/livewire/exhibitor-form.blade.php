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

    <!-- Resume Link Callout -->
    @if ($showResumeLink && $resumeToken)
        <flux:callout variant="warning" class="mb-6" icon="bookmark">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                    <div class="font-semibold">Save Your Progress</div>
                    <div class="mt-1 text-sm">
                        Your progress is auto-saved! You can bookmark this page or copy the URL from your browser's
                        address bar to resume later. Your text data is saved, but <strong>images will not be
                            saved</strong> in the draft.
                    </div>
                    <div class="mt-3">
                        <flux:input readonly :value="$this->resumeUrl" class="font-mono text-sm"
                            id="resume-url-input" />
                    </div>
                </div>
                <div>
                    <flux:button size="sm" variant="primary" icon="clipboard" x-data
                        x-on:click="
                        navigator.clipboard.writeText('{{ $this->resumeUrl }}');
                        $dispatch('resume-link-copied');
                    ">
                        Copy Link
                    </flux:button>
                </div>
            </div>
        </flux:callout>

        <!-- Toast notification for copy -->
        <div x-data="{ show: false }" x-on:resume-link-copied.window="show = true; setTimeout(() => show = false, 3000)"
            x-show="show" x-transition
            class="fixed bottom-4 right-4 z-50 rounded-lg bg-green-600 px-4 py-3 text-white shadow-lg dark:bg-green-500"
            style="display: none;">
            <div class="flex items-center gap-2">
                <flux:icon.check class="size-5" />
                <span>Resume link copied to clipboard!</span>
            </div>
        </div>
    @endif

    <!-- Progress Indicator -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            @for ($i = 1; $i <= 4; $i++)
                <button type="button" wire:click="goToStep({{ $i }})"
                    class="{{ implode(
                        ' ',
                        array_filter([
                            'flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition',
                            $currentStep === $i ? 'bg-zinc-100 text-zinc-900 dark:bg-zinc-800 dark:text-white' : '',
                            in_array($i, $completedSteps) && $currentStep !== $i ? 'text-green-600 dark:text-green-500' : '',
                            !in_array($i, $completedSteps) && $currentStep !== $i ? 'text-zinc-400 dark:text-zinc-600' : '',
                        ]),
                    ) }}">
                    <span
                        class="{{ implode(
                            ' ',
                            array_filter([
                                'flex h-6 w-6 shrink-0 items-center justify-center rounded-full',
                                $currentStep === $i ? 'bg-zinc-600 text-white dark:bg-zinc-400 dark:text-zinc-900' : '',
                                in_array($i, $completedSteps) && $currentStep !== $i ? 'bg-green-600 text-white dark:bg-green-500' : '',
                                !in_array($i, $completedSteps) && $currentStep !== $i
                                    ? 'bg-zinc-200 text-zinc-400 dark:bg-zinc-800 dark:text-zinc-600'
                                    : '',
                            ]),
                        ) }}">
                        @if (in_array($i, $completedSteps))
                            <flux:icon.check variant="outline" class="size-4" />
                        @elseif ($currentStep === $i)
                            <flux:icon.pencil variant="outline" class="size-4" />
                        @else
                            <flux:icon.ellipsis-horizontal variant="outline" class="size-4" />
                        @endif
                    </span>
                    <span>
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
                                'mx-2 h-0.5 flex-1',
                                in_array($i, $completedSteps) ? 'bg-green-600 dark:bg-green-500' : 'bg-zinc-300 dark:bg-zinc-700',
                            ]),
                        ) }}">
                    </div>
                @endif
            @endfor
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
                        <div>
                            <flux:file-upload wire:model="logo" label="Upload Logo"
                                description="High-quality PNG or JPG. Transparent background preferred."
                                accept="image/png,image/jpeg,image/jpg">
                                <flux:file-upload.dropzone heading="Drop file or click to browse"
                                    text="PNG or JPG, max 2MB" icon="photo" with-progress />
                            </flux:file-upload>

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
                        </div>

                        <!-- Brochure Upload -->
                        <div>
                            <flux:file-upload wire:model="brochure" label="Upload Brochure"
                                description="PDF visitors can view or download from your profile."
                                accept="application/pdf" badge="Optional">
                                <flux:file-upload.dropzone heading="Drop file or click to browse" text="PDF, max 10MB"
                                    icon="document-text" with-progress />
                            </flux:file-upload>

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
                        </div>
                    </div>

                    <!-- Photos Upload -->
                    <div>
                        <flux:file-upload wire:model="photos" label="Upload Images"
                            description="Upload 3–5 photos that best showcase your company or projects. Minimum 3 required."
                            accept="image/png,image/jpeg,image/jpg" multiple required>
                            <flux:file-upload.dropzone heading="Drop file or click to browse"
                                text="PNG or JPG, 3–5 photos required, 2MB each" icon="photo" with-progress />
                        </flux:file-upload>

                        @if (count($photos) > 0)
                            <div class="mt-3 space-y-3">
                                @foreach ($photos as $index => $photo)
                                    <div>
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
                            <flux:input wire:model="social_media_links.facebook" type="url"
                                placeholder="https://facebook.com/yourcompany" badge="Optional">
                                <x-slot name="iconLeading">
                                    <x-bi-facebook />
                                </x-slot>
                            </flux:input>

                            <flux:input wire:model="social_media_links.instagram" type="url"
                                placeholder="https://instagram.com/yourcompany" badge="Optional">
                                <x-slot name="iconLeading">
                                    <x-bi-instagram />
                                </x-slot>
                            </flux:input>

                            <flux:input wire:model="social_media_links.linkedin" type="url"
                                placeholder="https://linkedin.com/company/yourcompany" badge="Optional">
                                <x-slot name="iconLeading">
                                    <x-bi-linkedin />
                                </x-slot>
                            </flux:input>

                            <flux:input wire:model="social_media_links.twitter" type="url"
                                placeholder="https://x.com/yourcompany" badge="Optional">
                                <x-slot name="iconLeading">
                                    <x-bi-twitter-x />
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
                @else
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
</div>
