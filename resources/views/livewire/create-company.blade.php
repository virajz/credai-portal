<div class="mx-auto w-full max-w-3xl">
    <div class="mb-6">
        <flux:heading size="xl">Add New Company</flux:heading>
        <flux:subheading class="mt-2">Enter company details and stall allocation information</flux:subheading>
    </div>

    <form wire:submit="save">
        <flux:card class="space-y-6">
            <!-- Company Information -->
            <div>
                <flux:heading size="lg" class="mb-4">Company Information</flux:heading>

                <div class="space-y-4">
                    <flux:input wire:model="company_name" label="Company Name" placeholder="Enter company name" />

                    <div class="grid gap-4 md:grid-cols-2">
                        <flux:input wire:model="main_person_name" label="Main Person Name"
                            placeholder="Enter main person name" />

                        <flux:input wire:model="registered_number" label="Registered Number (Contact)"
                            placeholder="Enter contact number" type="tel" />
                    </div>
                </div>
            </div>

            <!-- Stall Details -->
            <div>
                <flux:heading size="lg" class="mb-4">Stall Details</flux:heading>

                <div class="space-y-4">
                    <div class="grid gap-4 md:grid-cols-2">
                        <flux:input wire:model="stall_type" label="Stall Type"
                            placeholder="e.g., Corner, Island, Linear" badge="Optional" />

                        <flux:input wire:model="stall_number" label="Stall Number" placeholder="e.g., A-101"
                            badge="Optional" />
                    </div>

                    <flux:input wire:model="stall_size" label="Stall Size" placeholder="e.g., 3m x 3m, 50 sq ft"
                        badge="Optional" />
                </div>
            </div>

            <!-- Payment Information -->
            <div>
                <flux:heading size="lg" class="mb-4">Payment Information</flux:heading>

                <div class="grid gap-4 md:grid-cols-3">
                    <flux:field>
                        <flux:label badge="Optional">Total Payment</flux:label>
                        <flux:input.group>
                            <flux:input.group.prefix>₹</flux:input.group.prefix>
                            <flux:input wire:model.blur="total_payment" placeholder="0.00" type="number" step="0.01"
                                min="0" />
                        </flux:input.group>
                        <flux:error name="total_payment" />
                    </flux:field>

                    <flux:field>
                        <flux:label badge="Optional">Payment Received</flux:label>
                        <flux:input.group>
                            <flux:input.group.prefix>₹</flux:input.group.prefix>
                            <flux:input wire:model.blur="payment_received" placeholder="0.00" type="number"
                                step="0.01" min="0" />
                        </flux:input.group>
                        <flux:error name="payment_received" />
                    </flux:field>

                    <flux:field>
                        <flux:label badge="Auto Calculated">Payment Pending</flux:label>
                        <flux:input.group>
                            <flux:input.group.prefix>₹</flux:input.group.prefix>
                            <flux:input wire:model="payment_pending" placeholder="0.00" type="number" step="0.01"
                                readonly />
                        </flux:input.group>
                        <flux:error name="payment_pending" />
                    </flux:field>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between border-t border-zinc-200 pt-6 dark:border-zinc-700">
                <flux:button variant="ghost" :href="route('companies.index')" wire:navigate>
                    Cancel
                </flux:button>
                <flux:button type="submit" variant="primary">
                    Create Company
                </flux:button>
            </div>
        </flux:card>
    </form>
</div>
