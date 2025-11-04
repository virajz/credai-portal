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
