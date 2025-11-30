<div class="mx-auto w-full max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="mb-12 text-center">
        <!-- Breadcrumb -->
        <div
            class="mb-8 inline-flex items-center rounded-full border border-emerald-200/50 bg-emerald-100/80 px-4 py-2 backdrop-blur-sm dark:border-emerald-800/30 dark:bg-emerald-900/20">
            <a href="/"
                class="text-sm font-medium text-emerald-700 hover:text-emerald-800 dark:text-emerald-300 dark:hover:text-emerald-200">Home</a>
            <flux:icon.chevron-right class="mx-2 size-4 text-emerald-600 dark:text-emerald-400" />
            <span class="text-sm font-semibold text-emerald-800 dark:text-emerald-300">Visitor Registration</span>
        </div>

        <flux:heading size="2xl" class="mb-6">
            <span
                class="bg-gradient-to-r from-emerald-700 via-emerald-600 to-teal-600 bg-clip-text text-transparent dark:from-emerald-400 dark:via-emerald-300 dark:to-teal-300">
                Visitor Registration
            </span>
        </flux:heading>

        <flux:subheading size="lg" class="mx-auto mb-12 max-w-4xl">
            Register for your free pass to CREDAI Glam 2026 - Surat's biggest property show
        </flux:subheading>
    </div>

    <flux:card class="backdrop-blur-sm">
        <!-- Progress Steps -->
        <div class="mb-8">
            <div class="flex items-center justify-center gap-4 md:gap-8">
                <!-- Step 1 -->
                <div class="flex items-center">
                    <div
                        class="flex size-10 items-center justify-center rounded-full {{ $currentStep >= 1 ? 'bg-emerald-600 text-white' : 'bg-zinc-200 text-zinc-500 dark:bg-zinc-700 dark:text-zinc-400' }}">
                        @if ($currentStep > 1)
                            <flux:icon.check class="size-5" />
                        @else
                            <span class="text-sm font-semibold">1</span>
                        @endif
                    </div>
                    <span
                        class="ml-2 text-xs md:text-sm font-medium {{ $currentStep >= 1 ? 'text-emerald-600 dark:text-emerald-400' : 'text-zinc-500 dark:text-zinc-400' }}"><span
                            class="hidden sm:inline">Personal Information</span><span
                            class="sm:hidden">Info</span></span>
                </div>

                <!-- Divider -->
                <div
                    class="h-0.5 w-8 md:flex-1 {{ $currentStep > 1 ? 'bg-emerald-600' : 'bg-zinc-200 dark:bg-zinc-700' }}">
                </div>

                <!-- Step 2 -->
                <div class="flex items-center">
                    <div
                        class="flex size-10 items-center justify-center rounded-full {{ $currentStep >= 2 ? 'bg-emerald-600 text-white' : 'bg-zinc-200 text-zinc-500 dark:bg-zinc-700 dark:text-zinc-400' }}">
                        <span class="text-sm font-semibold">2</span>
                    </div>
                    <span
                        class="ml-2 text-xs md:text-sm font-medium {{ $currentStep >= 2 ? 'text-emerald-600 dark:text-emerald-400' : 'text-zinc-500 dark:text-zinc-400' }}"><span
                            class="hidden sm:inline">Interests & Preferences</span><span
                            class="sm:hidden">Interests</span></span>
                </div>
            </div>
        </div>

        <form wire:submit.prevent="{{ $currentStep == 2 ? 'submit' : 'nextStep' }}" class="space-y-8">

            @if ($currentStep == 1)
                <!-- Step 1: Personal Information -->
                <div class="space-y-6">
                    <flux:heading size="lg" class="border-b border-zinc-200 pb-3 dark:border-zinc-700">
                        Tell us about yourself
                    </flux:heading>

                    <div class="grid grid-cols-1 items-start gap-6 md:grid-cols-2">
                        <!-- Name -->
                        <flux:field>
                            <flux:label>Full Name <span class="text-red-500">*</span></flux:label>
                            <flux:input wire:model.blur="name" placeholder="Enter your full name" />
                            <flux:error name="name" />
                        </flux:field>

                        <!-- Phone -->
                        <flux:field>
                            <flux:label>Phone Number <span class="text-red-500">*</span></flux:label>
                            <flux:input type="number" wire:model.blur="phone" placeholder="9876543210" maxlength="10"
                                inputmode="numeric" pattern="[0-9]*" />
                            <flux:error name="phone" />
                        </flux:field>
                    </div>

                    <div class="grid grid-cols-1 items-start gap-6 md:grid-cols-2">
                        <!-- Company Name -->
                        <flux:field>
                            <flux:label>Company Name <flux:badge size="sm" color="zinc">Optional</flux:badge>
                            </flux:label>
                            <flux:input wire:model.blur="company_name" placeholder="Enter your company name" />
                            <flux:error name="company_name" />
                        </flux:field>

                        <!-- Email -->
                        <flux:field>
                            <flux:label>Email Address <flux:badge size="sm" color="zinc">Optional</flux:badge>
                            </flux:label>
                            <flux:input wire:model.blur="email" type="email" placeholder="your.email@example.com" />
                            <flux:error name="email" />
                        </flux:field>
                    </div>

                    <!-- Photo -->
                    <flux:field>
                        <flux:label>Photo <flux:badge size="sm" color="zinc">Optional</flux:badge>
                        </flux:label>
                        <flux:input wire:model="photo" type="file" accept="image/*" />
                        <flux:error name="photo" />
                        @if ($photo)
                            <div class="mt-3">
                                <img src="{{ $photo->temporaryUrl() }}"
                                    class="size-24 rounded-lg border-2 border-zinc-200 object-cover dark:border-zinc-700">
                            </div>
                        @endif
                    </flux:field>
                </div>
            @endif

            @if ($currentStep == 2)
                <!-- Step 2: Interests & Preferences -->
                <div class="space-y-8">
                    <flux:heading size="lg" class="border-b border-zinc-200 pb-3 dark:border-zinc-700">
                        Your Property Interests
                    </flux:heading>

                    <!-- Property Types -->
                    <flux:field>
                        <flux:label>Property Types <span class="text-red-500">*</span></flux:label>
                        <flux:checkbox.group wire:model.live="interests">
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                @foreach (['Residential', 'Commercial', 'Plotting'] as $interest)
                                    <flux:checkbox value="{{ $interest }}" label="{{ $interest }}" />
                                @endforeach
                            </div>
                        </flux:checkbox.group>
                        <flux:error name="interests" />
                    </flux:field>

                    <!-- Sub-options for Residential -->
                    @if (in_array('Residential', $interests))
                        <div class="space-y-4 rounded-lg bg-emerald-50 p-4 dark:bg-emerald-950/20">
                            <flux:label class="text-sm font-semibold">Residential preferences: <span
                                    class="text-red-500">*</span></flux:label>
                            <flux:checkbox.group wire:model="residential_types">
                                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
                                    @foreach (['2 BHK', '3 BHK', '4 BHK', '5 BHK', 'Others'] as $type)
                                        <flux:checkbox value="{{ $type }}" label="{{ $type }}"
                                            variant="cards" />
                                    @endforeach
                                </div>
                            </flux:checkbox.group>
                            <flux:error name="residential_types" />
                        </div>
                    @endif

                    <!-- Sub-options for Commercial -->
                    @if (in_array('Commercial', $interests))
                        <div class="space-y-4 rounded-lg bg-blue-50 p-4 dark:bg-blue-950/20">
                            <flux:label class="text-sm font-semibold">Commercial preferences: <span
                                    class="text-red-500">*</span></flux:label>
                            <flux:checkbox.group wire:model="commercial_types">
                                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                                    @foreach (['Showroom', 'Shops', 'Offices', 'Others'] as $type)
                                        <flux:checkbox value="{{ $type }}" label="{{ $type }}"
                                            variant="cards" />
                                    @endforeach
                                </div>
                            </flux:checkbox.group>
                            <flux:error name="commercial_types" />
                        </div>
                    @endif

                    <!-- Sub-options for Plotting -->
                    @if (in_array('Plotting', $interests))
                        <div class="space-y-4 rounded-lg bg-purple-50 p-4 dark:bg-purple-950/20">
                            <flux:label class="text-sm font-semibold">Plotting preferences: <span
                                    class="text-red-500">*</span></flux:label>
                            <flux:checkbox.group wire:model="plotting_types">
                                <div class="grid grid-cols-2 gap-3">
                                    @foreach (['Industrial', 'Open'] as $type)
                                        <flux:checkbox value="{{ $type }}" label="{{ $type }}"
                                            variant="cards" />
                                    @endforeach
                                </div>
                            </flux:checkbox.group>
                            <flux:error name="plotting_types" />
                        </div>
                    @endif

                    <!-- Purchase Timeline -->
                    <flux:field>
                        <flux:label>Purchase Timeline <span class="text-red-500">*</span></flux:label>
                        <flux:radio.group wire:model="planning_to_buy" variant="cards"
                            class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <flux:radio value="Within 3 months" label="Within 3 months" />
                            <flux:radio value="Within 6 months" label="Within 6 months" />
                            <flux:radio value="Within a year" label="Within a year" />
                        </flux:radio.group>
                        <flux:error name="planning_to_buy" />
                    </flux:field>

                    <!-- Preferred Areas -->
                    <flux:field>
                        <flux:label>Preferred Areas <span class="text-red-500">*</span></flux:label>
                        <flux:checkbox.group wire:model="areas">
                            <div class="grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-4">
                                @foreach (['Athwa - Vesu', 'Pal - Adajan - Rander', 'Katargam', 'Varachha', 'Udhna - Sachin', 'Dindoli', 'Kamrej', 'Saroli', 'Within City', 'Outer City', 'Puna Kumbhaiya'] as $area)
                                    <flux:checkbox value="{{ $area }}" label="{{ $area }}" />
                                @endforeach
                            </div>
                        </flux:checkbox.group>
                        <flux:error name="areas" />
                    </flux:field>
                </div>
            @endif

            <!-- Navigation Buttons -->
            <div class="flex justify-between border-t border-zinc-200 pt-6 dark:border-zinc-700">
                @if ($currentStep > 1)
                    <flux:button type="button" wire:click="previousStep" variant="ghost" icon="chevron-left">
                        Previous
                    </flux:button>
                @else
                    <div></div>
                @endif

                @if ($currentStep == 1)
                    <flux:button type="submit" variant="primary" icon:trailing="chevron-right">
                        <span wire:loading.remove wire:target="nextStep">Continue</span>
                        <span wire:loading wire:target="nextStep">Processing...</span>
                    </flux:button>
                @else
                    <flux:button type="submit" variant="primary" icon="ticket">
                        <span wire:loading.remove wire:target="submit">Get Your Free Pass</span>
                        <span wire:loading wire:target="submit">Registering...</span>
                    </flux:button>
                @endif
            </div>
        </form>
    </flux:card>
</div>
