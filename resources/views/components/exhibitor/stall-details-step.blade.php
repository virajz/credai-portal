@if ($currentStep === 2)
    <flux:card class="space-y-6">
        <div>
            <flux:heading size="lg">Stall Details</flux:heading>
            <flux:text class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                Provide information about your exhibition stall booking and requirements.
            </flux:text>
        </div>

        <flux:separator />

        <!-- Stall Configuration -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <flux:input wire:model="stall_type" label="Stall Type" type="text" placeholder="e.g., Corner, Island, Linear"
                badge="Optional" />

            <flux:input wire:model="stall_number" label="Stall Number" type="text" placeholder="e.g., A-101"
                badge="Optional" />
        </div>

        <flux:input wire:model="stall_size" label="Stall Size" type="text" placeholder="e.g., 3m x 3m, 50 sq ft"
            badge="Optional" />

        <!-- Payment Details -->
        <div>
            <flux:heading size="base">Payment Information</flux:heading>
            <flux:text class="mb-4 mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                Enter payment details for the stall booking.
            </flux:text>

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

        <!-- Additional Requirements -->
        <flux:textarea wire:model="extra_furniture_details" label="Extra Furniture Details"
            placeholder="List any additional furniture or equipment required..."
            description="Specify any special furniture needs beyond the standard stall package." rows="3"
            badge="Optional" />

        <flux:textarea wire:model="exhibitor_passes_details" label="Exhibitor Passes Details"
            placeholder="Number and type of passes required..."
            description="Provide details about exhibitor passes needed for your team." rows="3"
            badge="Optional" />

        <flux:textarea wire:model="car_pass_details" label="Car Pass Details"
            placeholder="Number of car passes and vehicle details..."
            description="Specify car pass requirements for the exhibition." rows="3" badge="Optional" />

        <!-- Branding -->
        <flux:field>
            <flux:label>Use Brand Name as Fascia</flux:label>
            <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">
                Display your brand name on the stall fascia board.
            </flux:text>
            <flux:switch wire:model.live="use_brand_name_as_facia" />
        </flux:field>

        @if (!$use_brand_name_as_facia)
            <flux:input wire:model="facia_name" label="Fascia Name" type="text"
                placeholder="Enter custom fascia name"
                description="The name that will be displayed on your stall fascia board." />
        @endif

        <flux:input wire:model="momento_name" label="Momento Name" type="text"
            placeholder="Name to be engraved on momento" badge="Optional"
            description="The name that will appear on the commemorative momento." />
    </flux:card>
@endif
