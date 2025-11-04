<div class="mx-auto w-full max-w-7xl">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl" class="mb-2">Exhibitors</flux:heading>
            <flux:subheading>Manage all registered exhibitors for the exhibition</flux:subheading>
        </div>
        <flux:button variant="primary" :href="route('exhibitor.register')" icon="plus" wire:navigate>
            Add Exhibitor
        </flux:button>
    </div>

    <!-- Search and Filters -->
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end">
        <div class="flex-1">
            <flux:input wire:model.live.debounce.300ms="search"
                placeholder="Search by brand, contact, email, phone, or city..." icon="magnifying-glass" />
        </div>

        <div class="w-full lg:w-64">
            <flux:select wire:model.live="cityFilter" placeholder="All Cities" variant="listbox">
                <flux:select.option value="">All Cities</flux:select.option>
                @foreach ($cities as $city)
                    <flux:select.option :value="$city">{{ $city }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>

        @if ($search || $cityFilter)
            <flux:button variant="ghost" icon="x-mark" wire:click="clearFilters">
                Clear Filters
            </flux:button>
        @endif
    </div>

    <!-- Exhibitors Table -->
    <flux:card>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>
                    <button wire:click="sortByColumn('brand_name')"
                        class="flex items-center gap-1 hover:text-zinc-900 dark:hover:text-white">
                        Brand Name
                        @if ($sortBy === 'brand_name')
                            @if ($sortDirection === 'asc')
                                <flux:icon.chevron-up variant="micro" />
                            @else
                                <flux:icon.chevron-down variant="micro" />
                            @endif
                        @endif
                    </button>
                </flux:table.column>
                <flux:table.column>
                    <button wire:click="sortByColumn('city')"
                        class="flex items-center gap-1 hover:text-zinc-900 dark:hover:text-white">
                        City
                        @if ($sortBy === 'city')
                            @if ($sortDirection === 'asc')
                                <flux:icon.chevron-up variant="micro" />
                            @else
                                <flux:icon.chevron-down variant="micro" />
                            @endif
                        @endif
                    </button>
                </flux:table.column>
                <flux:table.column>Contact Person</flux:table.column>
                <flux:table.column>Phone</flux:table.column>
                <flux:table.column>Email</flux:table.column>
                <flux:table.column>
                    <button wire:click="sortByColumn('created_at')"
                        class="flex items-center gap-1 hover:text-zinc-900 dark:hover:text-white">
                        Registered
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
                @forelse ($exhibitors as $exhibitor)
                    <flux:table.row :key="$exhibitor->id">
                        <flux:table.cell>
                            <div class="flex items-center gap-3">
                                @if ($exhibitor->logo_path)
                                    <img src="{{ Storage::url($exhibitor->logo_path) }}"
                                        alt="{{ $exhibitor->brand_name }}"
                                        class="h-10 w-10 shrink-0 rounded-lg object-cover" />
                                @else
                                    <div
                                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-zinc-200 dark:bg-zinc-700">
                                        <flux:icon.building-office variant="micro" />
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <div class="truncate font-semibold">{{ $exhibitor->brand_name }}</div>
                                    <div class="truncate text-xs text-zinc-500">{{ $exhibitor->facia_name }}</div>
                                </div>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>{{ $exhibitor->city }}</flux:table.cell>
                        <flux:table.cell>{{ $exhibitor->contact_person_name }}</flux:table.cell>
                        <flux:table.cell>
                            <a href="tel:{{ $exhibitor->phone_number }}" class="hover:underline">
                                {{ $exhibitor->phone_number }}
                            </a>
                        </flux:table.cell>
                        <flux:table.cell>
                            @if ($exhibitor->email)
                                <a href="mailto:{{ $exhibitor->email }}" class="hover:underline">
                                    {{ $exhibitor->email }}
                                </a>
                            @else
                                <span class="text-zinc-400">—</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <time datetime="{{ $exhibitor->created_at->toISOString() }}" class="text-sm">
                                {{ $exhibitor->created_at->format('M d, Y') }}
                            </time>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:dropdown position="bottom" align="end">
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                                <flux:menu class="w-40">
                                    <flux:menu.item icon="eye">View Details</flux:menu.item>
                                    <flux:menu.item icon="pencil">Edit</flux:menu.item>
                                    <flux:menu.separator />
                                    <flux:menu.item icon="trash" variant="danger">Delete</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7" class="text-center">
                            <div class="py-12">
                                <flux:icon.inbox class="mx-auto mb-4 text-zinc-400" variant="outline" />
                                <flux:heading size="lg" class="mb-2">No exhibitors found</flux:heading>
                                <flux:subheading class="mb-4">
                                    @if ($search || $cityFilter)
                                        Try adjusting your search or filter criteria
                                    @else
                                        Get started by adding your first exhibitor
                                    @endif
                                </flux:subheading>
                                @if (!$search && !$cityFilter)
                                    <flux:button variant="primary" :href="route('exhibitor.register')" icon="plus"
                                        wire:navigate>
                                        Add Exhibitor
                                    </flux:button>
                                @endif
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        @if ($exhibitors->hasPages())
            <div class="border-t border-zinc-200 px-6 py-4 dark:border-zinc-700">
                {{ $exhibitors->links() }}
            </div>
        @endif
    </flux:card>

    <!-- Stats -->
    @if ($exhibitors->total() > 0)
        <div class="mt-4 text-sm text-zinc-600 dark:text-zinc-400">
            Showing {{ $exhibitors->firstItem() }} to {{ $exhibitors->lastItem() }} of {{ $exhibitors->total() }}
            exhibitors
        </div>
    @endif
</div>
