<div class="mx-auto w-full">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl" class="mb-2">Companies</flux:heading>
            <flux:subheading>Manage companies and their stall allocations</flux:subheading>
        </div>
        <div class="flex gap-2">
            <flux:button variant="ghost" icon="arrow-down-tray" wire:click="exportCompanies">
                Export
            </flux:button>
            <flux:button variant="primary" :href="route('companies.create')" icon="plus" wire:navigate>
                Add Company
            </flux:button>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end">
        <div class="flex-1">
            <flux:input wire:model.live.debounce.300ms="search"
                placeholder="Search by company name, main person, registered number, or stall number..."
                icon="magnifying-glass" />
        </div>

        <div class="flex gap-2">
            <flux:button variant="ghost" icon="funnel" icon:variant="outline" wire:click="toggleFilterDrawer">
                Filters
                @if ($this->hasActiveFilters())
                    <flux:badge size="sm" color="blue">
                        {{ collect([$filterStatus, $filterLockStatus, $filterStallAssignment])->filter()->count() }}
                    </flux:badge>
                @endif
            </flux:button>

            @if ($this->hasActiveFilters())
                <flux:button variant="ghost" icon="x-mark" wire:click="clearFilters">
                    Clear All
                </flux:button>
            @endif
        </div>
    </div>

    <!-- Companies Table -->
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
            <flux:table.column>Category</flux:table.column>
            <flux:table.column>Main Person</flux:table.column>
            <flux:table.column>Registered Number</flux:table.column>
            <flux:table.column>Stall Details</flux:table.column>
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
                        <div class="flex items-center gap-2">
                            <div class="font-semibold">
                                <a href="{{ route('companies.edit', $company->id) }}"
                                    wire:navigate>{{ $company->company_name }}</a>
                            </div>
                            @if ($company->has_submitted)
                                <flux:badge size="sm" color="green" icon="check-circle">Submitted</flux:badge>
                            @endif
                            @if ($company->is_locked)
                                <flux:badge size="sm" color="red" icon="lock-closed">Locked</flux:badge>
                            @endif
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm" :color="$company->category === 'Builders' ? 'blue' : 'purple'">
                            {{ $company->category }}
                        </flux:badge>
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
                        <time datetime="{{ $company->created_at->toISOString() }}" class="text-sm">
                            {{ $company->created_at->format('M d, Y') }}
                        </time>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="flex items-center gap-2" x-data="{ copied: false }"
                            @copy-to-clipboard.window="if ($event.detail.url) { navigator.clipboard.writeText($event.detail.url).then(() => { copied = true; setTimeout(() => copied = false, 2000) }) }">
                            <flux:button wire:click="generateAndCopyLink({{ $company->id }})" variant="ghost"
                                size="sm" icon="clipboard" icon:variant="outline"
                                x-tooltip="copied ? 'Copied!' : 'Copy registration link'" />

                            <flux:button wire:click="toggleLock({{ $company->id }})" variant="ghost" size="sm"
                                :icon="$company->is_locked ? 'lock-closed' : 'lock-open'" icon:variant="outline"
                                x-tooltip="'{{ $company->is_locked ? 'Unlock registration link' : 'Lock registration link' }}'" />

                            <flux:dropdown position="bottom" align="end">
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                                <flux:menu class="w-48">
                                    @if ($company->has_submitted)
                                        <flux:menu.item icon="eye" icon:variant="outline"
                                            :href="route('companies.submission', $company)" wire:navigate>View
                                            Submission</flux:menu.item>
                                    @endif
                                    <flux:menu.item icon="pencil" icon:variant="outline"
                                        :href="route('companies.edit', $company)" wire:navigate>Edit</flux:menu.item>
                                    <flux:menu.separator />
                                    <flux:menu.item icon="trash" icon:variant="outline" variant="danger"
                                        wire:click="confirmDelete({{ $company->id }})">Delete</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="8" class="text-center">
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
        <div class="border-t border-zinc-200 py-4 dark:border-zinc-700">
            {{ $companies->links() }}
        </div>
    @endif

    <!-- Filter Drawer -->
    <flux:modal name="filter-drawer" variant="flyout" wire:model="filterDrawerOpen">
        <form class="space-y-6" wire:submit="applyFilters">
            <div>
                <flux:heading size="lg">Filter Companies</flux:heading>
                <flux:subheading>Refine your company list with advanced filters</flux:subheading>
            </div>

            <flux:separator />

            <!-- Submission Status Filter -->
            <flux:field>
                <flux:label>Submission Status</flux:label>
                <flux:radio.group wire:model.live="filterStatus">
                    <flux:radio value="" label="All Companies" />
                    <flux:radio value="submitted" label="Submitted" />
                    <flux:radio value="pending" label="Pending" />
                </flux:radio.group>
            </flux:field>

            <flux:separator />

            <!-- Lock Status Filter -->
            <flux:field>
                <flux:label>Lock Status</flux:label>
                <flux:radio.group wire:model.live="filterLockStatus">
                    <flux:radio value="" label="All" />
                    <flux:radio value="locked" label="Locked" />
                    <flux:radio value="unlocked" label="Unlocked" />
                </flux:radio.group>
            </flux:field>

            <flux:separator />

            <!-- Stall Assignment Filter -->
            <flux:field>
                <flux:label>Stall Assignment</flux:label>
                <flux:radio.group wire:model.live="filterStallAssignment">
                    <flux:radio value="" label="All" />
                    <flux:radio value="assigned" label="Assigned" />
                    <flux:radio value="unassigned" label="Not Assigned" />
                </flux:radio.group>
            </flux:field>

            <flux:separator />

            <div class="flex gap-2">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="clearFilters" type="button">Clear All</flux:button>
                <flux:button variant="primary" type="submit">Apply Filters</flux:button>
            </div>
        </form>
    </flux:modal>

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
