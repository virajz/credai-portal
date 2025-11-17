<div class="mx-auto w-full max-w-3xl">
    <div class="mb-8">
        <flux:heading size="xl" class="mb-2">
            {{ $company?->exists ? 'Edit Company' : 'Add New Company' }}
        </flux:heading>
        <flux:subheading>Enter company details and stall allocation information</flux:subheading>
    </div>

    <form wire:submit="save">
        <flux:card class="space-y-6">
            <!-- Company Information -->
            <div>
                <flux:heading size="lg" class="mb-4">Company Information</flux:heading>

                <div class="space-y-4">
                    <flux:field>
                        <flux:label>Company Name <span class="text-red-500">*</span></flux:label>
                        <flux:input wire:model="company_name" placeholder="Enter company name" />
                        <flux:error name="company_name" />
                    </flux:field>

                    <div class="grid gap-4 md:grid-cols-2">
                        <flux:field>
                            <flux:label>Main Person Name <span class="text-red-500">*</span></flux:label>
                            <flux:input wire:model="main_person_name" placeholder="Enter main person name" />
                            <flux:error name="main_person_name" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Registered Number (Contact) <span class="text-red-500">*</span></flux:label>
                            <flux:input wire:model="registered_number" placeholder="Enter contact number"
                                type="tel" />
                            <flux:error name="registered_number" />
                            <flux:description>This will be used for OTP verification</flux:description>
                        </flux:field>
                    </div>
                </div>
            </div>

            <!-- Stall Details -->
            <div>
                <flux:heading size="lg" class="mb-4">Stall Details</flux:heading>

                <div class="space-y-4">
                    <div class="grid gap-4 md:grid-cols-2">
                        <flux:field>
                            <flux:label>Stall Type</flux:label>
                            <flux:input wire:model="stall_type" placeholder="e.g., Corner, Island, Linear" />
                            <flux:error name="stall_type" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Stall Number</flux:label>
                            <flux:input wire:model="stall_number" placeholder="e.g., A-101" />
                            <flux:error name="stall_number" />
                        </flux:field>
                    </div>

                    <flux:field>
                        <flux:label>Stall Size</flux:label>
                        <flux:input wire:model="stall_size" placeholder="e.g., 3m x 3m, 50 sq ft" />
                        <flux:error name="stall_size" />
                    </flux:field>
                </div>
            </div>

            <!-- Payment Information -->
            <div>
                <flux:heading size="lg" class="mb-4">Payment Information</flux:heading>

                <div class="space-y-4">
                    <div class="grid gap-4 md:grid-cols-3">
                        <flux:field>
                            <flux:label>Total Payment</flux:label>
                            <flux:input wire:model.blur="total_payment" placeholder="0.00" type="number" step="0.01"
                                min="0" icon="currency-rupee" icon-trailing />
                            <flux:error name="total_payment" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Payment Received</flux:label>
                            <flux:input wire:model.blur="payment_received" placeholder="0.00" type="number"
                                step="0.01" min="0" icon="currency-rupee" icon-trailing />
                            <flux:error name="payment_received" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Payment Pending</flux:label>
                            <flux:input wire:model="payment_pending" placeholder="0.00" type="number" step="0.01"
                                readonly icon="currency-rupee" icon-trailing />
                            <flux:error name="payment_pending" />
                            <flux:description>Auto-calculated</flux:description>
                        </flux:field>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between border-t border-zinc-200 pt-6 dark:border-zinc-700">
                <flux:button variant="ghost" :href="route('companies.index')" wire:navigate>
                    Cancel
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ $company?->exists ? 'Update Company' : 'Create Company' }}
                </flux:button>
            </div>
        </flux:card>
    </form>
</div>
