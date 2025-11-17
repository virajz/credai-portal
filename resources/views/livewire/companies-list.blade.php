<div class="mx-auto w-full max-w-7xl">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl" class="mb-2">Companies</flux:heading>
            <flux:subheading>Manage companies and their stall allocations</flux:subheading>
        </div>
        <flux:button variant="primary" :href="route('companies.create')" icon="plus" wire:navigate>
            Add Company
        </flux:button>
    </div>

    <!-- Search -->
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end">
        <div class="flex-1">
            <flux:input wire:model.live.debounce.300ms="search"
                placeholder="Search by company name, main person, registered number, or stall number..."
                icon="magnifying-glass" />
        </div>

        @if ($search)
            <flux:button variant="ghost" icon="x-mark" wire:click="clearFilters">
                Clear Filters
            </flux:button>
        @endif
    </div>

    <!-- Companies Table -->
    <flux:card>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>
                    <button wire:click="sortByColumn('company_name')"
                        class="flex items-center gap-1 hover:text-zinc-900 dark:hover:text-white">
                        Company Name
                        @if ($sortBy === 'company_name')
                            @if ($sortDirection === 'asc')
                                <flux:icon.chevron-up variant="micro" />
                            @else
                                <flux:icon.chevron-down variant="micro" />
                            @endif
                        @endif
                    </button>
                </flux:table.column>
                <flux:table.column>Main Person</flux:table.column>
                <flux:table.column>Registered Number</flux:table.column>
                <flux:table.column>Stall Details</flux:table.column>
                <flux:table.column>Payment Status</flux:table.column>
                <flux:table.column>
                    <button wire:click="sortByColumn('created_at')"
                        class="flex items-center gap-1 hover:text-zinc-900 dark:hover:text-white">
                        Created
                        @if ($sortBy === 'created_at')
                            @if ($sortDirection === 'asc')
                                <flux:icon.chevron-up variant="micro" />
                            @else
                                <flux:icon.chevron-down variant="micro" />
                            @endif
                        @endif
                    </button>
                </flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($companies as $company)
                    <flux:table.row :key="$company->id">
                        <flux:table.cell>
                            <div class="font-semibold">{{ $company->company_name }}</div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div>{{ $company->main_person_name }}</div>
                            <div class="text-xs text-zinc-500">{{ $company->registered_number }}</div>
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $company->registered_number }}
                        </flux:table.cell>
                        <flux:table.cell>
                            @if ($company->stall_number)
                                <div class="text-sm">
                                    <div class="font-medium">{{ $company->stall_number }}</div>
                                    @if ($company->stall_type || $company->stall_size)
                                        <div class="text-xs text-zinc-500">
                                            {{ $company->stall_type }}
                                            @if ($company->stall_type && $company->stall_size)
                                                •
                                            @endif
                                            {{ $company->stall_size }}
                                        </div>
                                    @endif
                                </div>
                            @else
                                <span class="text-zinc-400">Not assigned</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            @if ($company->total_payment)
                                <div class="text-sm">
                                    <div class="font-medium">₹{{ number_format($company->total_payment, 2) }}</div>
                                    @if ($company->payment_pending > 0)
                                        <flux:badge size="sm" color="yellow">
                                            ₹{{ number_format($company->payment_pending, 2) }}
                                            pending</flux:badge>
                                    @else
                                        <flux:badge size="sm" color="green">Paid</flux:badge>
                                    @endif
                                </div>
                            @else
                                <span class="text-zinc-400">—</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <time datetime="{{ $company->created_at->toISOString() }}" class="text-sm">
                                {{ $company->created_at->format('M d, Y') }}
                            </time>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:dropdown position="bottom" align="end">
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                                <flux:menu class="w-40">
                                    <flux:menu.item icon="pencil" :href="route('companies.edit', $company)"
                                        wire:navigate>Edit</flux:menu.item>
                                    <flux:menu.separator />
                                    <flux:menu.item icon="trash" variant="danger"
                                        wire:click="confirmDelete({{ $company->id }})">Delete</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7" class="text-center">
                            <div class="py-12">
                                <flux:icon.inbox class="mx-auto mb-4 text-zinc-400" variant="outline" />
                                <flux:heading size="lg" class="mb-2">No companies found</flux:heading>
                                <flux:subheading class="mb-4">
                                    @if ($search)
                                        Try adjusting your search criteria
                                    @else
                                        Get started by adding your first company
                                    @endif
                                </flux:subheading>
                                @if (!$search)
                                    <flux:button variant="primary" :href="route('companies.create')" icon="plus"
                                        wire:navigate>
                                        Add Company
                                    </flux:button>
                                @endif
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        @if ($companies->hasPages())
            <div class="border-t border-zinc-200 px-6 py-4 dark:border-zinc-700">
                {{ $companies->links() }}
            </div>
        @endif
    </flux:card>

    <!-- Stats -->
    @if ($companies->total() > 0)
        <div class="mt-4 text-sm text-zinc-600 dark:text-zinc-400">
            Showing {{ $companies->firstItem() }} to {{ $companies->lastItem() }} of {{ $companies->total() }}
            companies
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    <flux:modal name="delete-company" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete company?</flux:heading>
                <flux:text class="mt-2">
                    <p>You're about to delete this company.</p>
                    <p>This action cannot be reversed.</p>
                </flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="cancelDelete">Cancel</flux:button>
                <flux:button variant="danger" wire:click="deleteCompany">Delete company</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
