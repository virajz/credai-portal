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
            @for ($i = 1; $i <= 3; $i++)
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
                                    <flux:icon.photo class="size-3.5" />
                                @elseif ($i === 3)
                                    <flux:icon.sparkles class="size-3.5" />
                                @endif
                            </span>
                        @endif
                    </span>
                    <span class="hidden whitespace-nowrap md:inline">
                        @if ($i === 1)
                            Exhibitor Details
                        @elseif ($i === 2)
                            Stall Details
                        @elseif ($i === 3)
                            Project Details
                        @endif
                    </span>
                </button>

                @if ($i < 3)
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
                        <flux:icon.photo class="size-5" />
                    @elseif ($currentStep === 3)
                        <flux:icon.sparkles class="size-5" />
                    @endif
                </div>
                <div>
                    <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Step {{ $currentStep }} of 3
                    </div>
                    <div class="text-base font-semibold text-zinc-900 dark:text-white">
                        @if ($currentStep === 1)
                            Exhibitor Details
                        @elseif ($currentStep === 2)
                            Stall Details
                        @elseif ($currentStep === 3)
                            Project Details
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form wire:submit="submit" class="space-y-6">
        <!-- Step 1: Exhibitor Details -->
        @if ($currentStep === 1)
            <flux:card>
                <div class="space-y-6">
                    <flux:heading size="lg">Company Information</flux:heading>
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <flux:input wire:model="brand_name" label="Company / Brand Name" placeholder="ABC Developers"
                            required />

                        <flux:select wire:model="city" label="City" placeholder="Select city" variant="listbox"
                            searchable required>
                            <flux:select.option value="Surat">Surat</flux:select.option>
                            <flux:select.option value="Navsari">Navsari</flux:select.option>
                            <flux:select.option value="Ahmedabad">Ahmedabad</flux:select.option>
                            <flux:select.option value="Baroda">Baroda</flux:select.option>
                            <flux:select.option value="Others">Others</flux:select.option>
                        </flux:select>
                    </div>

                    <flux:textarea wire:model="office_address" label="Company Address"
                        placeholder="123 Main Street, Building Name, Area" rows="3" required />

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <flux:input wire:model="gst_number" label="GST Number" placeholder="22AAAAA0000A1Z5"
                            badge="Optional" />

                        <flux:input wire:model="pan_number" label="PAN Card" placeholder="AAAAA0000A"
                            badge="Optional" />
                    </div>

                    <flux:separator />

                    <flux:heading size="lg">Contact Information</flux:heading>
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <flux:input wire:model="contact_person_name" label="Main Contact Person" placeholder="John Doe"
                            required />

                        <flux:input wire:model="phone_number" label="Mobile Number" type="tel" inputmode="numeric"
                            placeholder="9876543210" required />
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <flux:input wire:model="email" label="Email ID" type="email" placeholder="contact@example.com"
                            badge="Optional" />

                        <flux:input wire:model="website" label="Website URL" type="url"
                            placeholder="https://www.example.com" badge="Optional" />
                    </div>

                    <!-- Media & Branding Section -->
                    @include('components.exhibitor.media-upload-section')
                </div>
            </flux:card>
        @endif

        <!-- Step 2: Stall Details -->
        @include('components.exhibitor.stall-details-step')

        <!-- Step 3: Project Details -->
        @include('components.exhibitor.projects-step')

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
                @if ($currentStep < 3)
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

                <!-- Modal -->
                <flux:modal x-model="showModal" name="save-progress-modal" class="space-y-6 md:hidden">
                    <div>
                        <flux:heading size="lg">Your progress is saved!</flux:heading>
                        <flux:subheading>You can continue filling this form later using the link below.
                        </flux:subheading>
                    </div>

                    <div class="space-y-2">
                        <flux:label>Resume Link</flux:label>
                        <div class="flex gap-2">
                            <flux:input value="{{ $this->resumeUrl }}" readonly class="font-mono text-xs" />
                            <flux:button @click="copyLink" icon="clipboard" size="sm" variant="ghost">
                                Copy
                            </flux:button>
                        </div>
                        <flux:text class="text-sm">Save this link or send it to your email to continue later.
                        </flux:text>
                    </div>

                    <div class="flex gap-2">
                        <flux:button @click="showModal = false" variant="primary" class="flex-1">
                            Got it
                        </flux:button>
                    </div>
                </flux:modal>
            </div>

            <!-- Desktop: Accordion in sidebar -->
            <div class="hidden md:block">
                <div class="fixed bottom-6 right-6 z-40 w-80">
                    <div class="overflow-hidden rounded-lg border border-emerald-200 bg-white shadow-lg dark:border-emerald-800 dark:bg-zinc-900"
                        :class="{ 'h-auto': expanded, 'h-14': !expanded }">
                        <!-- Header / Toggle -->
                        <button type="button" @click="expanded = !expanded"
                            class="flex w-full items-center justify-between p-4 transition hover:bg-emerald-50 dark:hover:bg-emerald-950/30">
                            <div class="flex items-center gap-3">
                                <flux:icon.bookmark class="size-5 text-emerald-600 dark:text-emerald-400" />
                                <flux:heading size="base" class="text-emerald-900 dark:text-emerald-100">
                                    Progress Saved
                                </flux:heading>
                            </div>
                            <flux:icon.chevron-down
                                class="size-5 text-emerald-600 transition-transform dark:text-emerald-400"
                                ::class="{ 'rotate-180': expanded }" />
                        </button>

                        <!-- Content -->
                        <div x-show="expanded" x-collapse
                            class="border-t border-emerald-100 p-4 dark:border-emerald-900">
                            <flux:subheading class="mb-4">
                                Your progress is automatically saved. Use this link to continue later.
                            </flux:subheading>

                            <div class="space-y-2">
                                <flux:label>Resume Link</flux:label>
                                <div class="flex gap-2">
                                    <flux:input value="{{ $this->resumeUrl }}" readonly class="font-mono text-xs" />
                                    <flux:button @click="copyLink" icon="clipboard" size="sm" variant="ghost">
                                        Copy
                                    </flux:button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Toast for link copied -->
            <div x-data="{ show: false }" @link-copied.window="show = true; setTimeout(() => show = false, 2000)"
                x-show="show" x-cloak x-transition
                class="fixed bottom-20 left-1/2 z-50 -translate-x-1/2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-lg dark:bg-emerald-500 md:bottom-auto md:left-auto md:right-6 md:top-6 md:translate-x-0">
                <div class="flex items-center gap-2">
                    <flux:icon.check class="size-4" />
                    <span>Link copied to clipboard!</span>
                </div>
            </div>
        </div>
    @endif
</div>
