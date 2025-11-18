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

            <!-- Payment Details (Read-only) -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <flux:field>
                    <flux:label>Total Payment</flux:label>
                    <flux:text>
                        {{ $this->total_payment ? '₹ ' . number_format((float) $this->total_payment, 2) : 'Not specified' }}
                    </flux:text>
                </flux:field>

                <flux:field>
                    <flux:label>Payment Received</flux:label>
                    <flux:text>
                        {{ $this->payment_received ? '₹ ' . number_format((float) $this->payment_received, 2) : '₹ 0.00' }}
                    </flux:text>
                </flux:field>

                <flux:field>
                    <flux:label>Payment Pending</flux:label>
                    <flux:text>
                        {{ $this->payment_pending ? '₹ ' . number_format((float) $this->payment_pending, 2) : '₹ 0.00' }}
                    </flux:text>
                </flux:field>
            </div>
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

            <!-- Payment Details (Editable) -->
            <div>
                <flux:heading size="base">Payment Information</flux:heading>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                    <flux:input wire:model="total_payment" label="Total Payment" type="number" step="0.01"
                        placeholder="0.00" badge="Optional">
                        <x-slot name="iconLeading">
                            <flux:icon.currency-rupee />
                        </x-slot>
                    </flux:input>

                    <flux:input wire:model="payment_received" label="Payment Received" type="number" step="0.01"
                        placeholder="0.00" badge="Optional">
                        <x-slot name="iconLeading">
                            <flux:icon.currency-rupee />
                        </x-slot>
                    </flux:input>

                    <flux:input wire:model="payment_pending" label="Payment Pending" type="number" step="0.01"
                        placeholder="0.00" badge="Optional">
                        <x-slot name="iconLeading">
                            <flux:icon.currency-rupee />
                        </x-slot>
                    </flux:input>
                </div>
            </div>
        @endif

        <!-- Additional Requirements -->
        <flux:textarea wire:model="extra_furniture_details" label="Extra Furniture Details"
            placeholder="List any additional furniture or equipment required..." rows="3" badge="Optional" />

        <flux:textarea wire:model="exhibitor_passes_details" label="Exhibitor Passes Details"
            placeholder="Number and type of passes required..." rows="3" badge="Optional" />

        <flux:textarea wire:model="car_pass_details" label="Car Pass Details"
            placeholder="Number of car passes and vehicle details..." rows="3" badge="Optional" />

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
