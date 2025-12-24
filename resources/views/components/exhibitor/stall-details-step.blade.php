@if ($currentStep === 2)
    <flux:card class="space-y-6">
        <div>
            <flux:heading size="lg">Stall Details</flux:heading>
            @if (isset($isReadOnly) && $isReadOnly)
                <flux:subheading class="mt-2">
                    These details have been pre-filled from your company registration and cannot be modified.
                </flux:subheading>
            @endif
        </div>

        <flux:separator />

        @if (isset($isReadOnly) && $isReadOnly)
            <!-- Stall Configuration (Read-only) -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <flux:field>
                    <flux:label>Stall Type</flux:label>
                    <flux:text>{{ $this->stall_type ?: 'Not assigned' }}</flux:text>
                </flux:field>

                <flux:field>
                    <flux:label>Stall Number</flux:label>
                    <flux:text>{{ $this->stall_number ?: 'Not assigned' }}</flux:text>
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Stall Size</flux:label>
                <flux:text>{{ $this->stall_size ?: 'Not assigned' }}</flux:text>
            </flux:field>
        @else
            <!-- Stall Configuration (Editable) -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <flux:input wire:model="stall_type" label="Stall Type" type="text"
                    placeholder="e.g., Corner, Island, Linear" badge="Optional" />

                <flux:input wire:model="stall_number" label="Stall Number" type="text" placeholder="e.g., A-101"
                    badge="Optional" />
            </div>

            <flux:input wire:model="stall_size" label="Stall Size" type="text" placeholder="e.g., 3m x 3m, 50 sq ft"
                badge="Optional" />
        @endif

        <!-- Additional Requirements -->
        <flux:textarea wire:model="extra_furniture_details" label="Extra Furniture Details"
            placeholder="List any additional furniture or equipment required..." rows="3" badge="Optional" />

        <!-- Branding -->
        <flux:field variant="inline">
            <flux:label>Use Brand Name as Fascia</flux:label>
            <flux:switch wire:model.live="use_brand_name_as_facia" />
        </flux:field>

        <flux:input wire:model="facia_name" label="Fascia Name" type="text" placeholder="Enter custom fascia name"
            :readonly="$use_brand_name_as_facia" />

        <flux:field variant="inline">
            <flux:label>Use Brand Name as Momento</flux:label>
            <flux:switch wire:model.live="use_brand_name_as_momento" />
        </flux:field>

        <flux:input wire:model="momento_name" label="Momento Name" type="text"
            placeholder="Name to be engraved on momento" badge="Optional" :readonly="$use_brand_name_as_momento" />
    </flux:card>
@endif
