<div class="mx-auto w-full max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="mb-12 text-center">
        <flux:heading size="2xl" class="mb-6">
            <span
                class="bg-gradient-to-r from-teal-700 via-teal-600 to-teal-600 bg-clip-text text-transparent dark:from-teal-400 dark:via-teal-300 dark:to-teal-300">
                Partner Registration
            </span>
        </flux:heading>

        <flux:subheading size="lg" class="mx-auto mb-12 max-w-4xl">
            Register as a partner for CREDAI Glam Property Show 2026
        </flux:subheading>
    </div>

    <flux:card class="backdrop-blur-sm">
        <form wire:submit.prevent="submit" class="space-y-8">
            <!-- Personal Information -->
            <div class="space-y-6">
                <flux:heading size="lg" class="border-b border-zinc-200 pb-3 dark:border-zinc-700">
                    Personal Information
                </flux:heading>

                <div class="grid grid-cols-1 items-start gap-6 md:grid-cols-2">
                    <!-- First Name -->
                    <flux:field>
                        <flux:label>First Name <span class="text-red-500">*</span></flux:label>
                        <flux:input wire:model.blur="first_name" placeholder="Enter your first name" />
                        <flux:error name="first_name" />
                    </flux:field>

                    <!-- Last Name -->
                    <flux:field>
                        <flux:label>Last Name <span class="text-red-500">*</span></flux:label>
                        <flux:input wire:model.blur="last_name" placeholder="Enter your last name" />
                        <flux:error name="last_name" />
                    </flux:field>
                </div>

                <div class="grid grid-cols-1 items-start gap-6 md:grid-cols-2">
                    <!-- Firm Name -->
                    <flux:field>
                        <flux:label>Firm Name <flux:badge size="sm" color="zinc">Optional</flux:badge>
                        </flux:label>
                        <flux:input wire:model.blur="firm_name" placeholder="Enter your firm name" />
                        <flux:error name="firm_name" />
                    </flux:field>

                    <!-- Email -->
                    <flux:field>
                        <flux:label>Email ID <flux:badge size="sm" color="zinc">Optional</flux:badge>
                        </flux:label>
                        <flux:input type="email" wire:model.blur="email" placeholder="your@email.com" />
                        <flux:error name="email" />
                    </flux:field>
                </div>

                <!-- Phone -->
                <flux:field>
                    <flux:label>Contact Number <span class="text-red-500">*</span></flux:label>
                    <flux:input type="number" wire:model.blur="phone" placeholder="9876543210" maxlength="10"
                        inputmode="numeric" pattern="[0-9]*" />
                    <flux:error name="phone" />
                </flux:field>
            </div>

            <!-- Interests & Preferences -->
            <div class="space-y-8">
                <flux:heading size="lg" class="border-b border-zinc-200 pb-3 dark:border-zinc-700">
                    Your Property Interests
                </flux:heading>

                <!-- Preferred Areas -->
                <flux:field>
                    <flux:label>Area of Interest <span class="text-red-500">*</span></flux:label>
                    <flux:checkbox.group wire:model="areas">
                        <div class="grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-4">
                            @foreach (['Athwa - Vesu', 'Pal - Adajan - Rander', 'Katargam', 'Olpad', 'Puna Kumbhaiya', 'Varachha', 'Udhna - Sachin', 'Dindoli', 'Kamrej', 'Saroli', 'Old City', 'Outer City Area'] as $area)
                                <flux:checkbox value="{{ $area }}" label="{{ $area }}" />
                            @endforeach
                        </div>
                    </flux:checkbox.group>
                    <flux:error name="areas" />
                </flux:field>

                <!-- Property Types -->
                <flux:field>
                    <flux:label>Type of Property <span class="text-red-500">*</span></flux:label>
                    <flux:checkbox.group wire:model="property_types">
                        <div class="grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-4">
                            @foreach (['Apartment', 'Bungalows', 'Plot', 'Farm House', 'Shops', 'Showrooms', 'Office Spaces'] as $type)
                                <flux:checkbox value="{{ $type }}" label="{{ $type }}" />
                            @endforeach
                        </div>
                    </flux:checkbox.group>
                    <flux:error name="property_types" />
                </flux:field>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end border-t border-zinc-200 pt-6 dark:border-zinc-700">
                <flux:button type="submit" variant="primary" icon="ticket" :disabled="$isSubmitting">
                    @if($isSubmitting)
                        <span>Registering...</span>
                    @else
                        <span>Register as Partner</span>
                    @endif
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>
