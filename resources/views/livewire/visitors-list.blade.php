<div class="mx-auto w-full">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl" class="mb-2">Visitors</flux:heading>
            <flux:subheading>Manage visitor registrations and information</flux:subheading>
        </div>
        <flux:button variant="primary" wire:click="exportVisitors" icon="arrow-down-tray" icon:variant="outline">
            Export Data
        </flux:button>
    </div>

    <!-- Search -->
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end">
        <div class="flex-1">
            <flux:input wire:model.live.debounce.300ms="search"
                placeholder="Search by name, email, phone, or company name..." icon="magnifying-glass" />
        </div>

        @if ($search)
            <flux:button variant="ghost" icon="x-mark" wire:click="clearFilters">
                Clear Filters
            </flux:button>
        @endif
    </div>

    <!-- Visitors Table -->
    <flux:table>
        <flux:table.columns>
            <flux:table.column>
                <button wire:click="sortByColumn('name')"
                    class="flex items-center gap-1 hover:text-zinc-900 dark:hover:text-white">
                    Name
                    @if ($sortBy === 'name')
                        @if ($sortDirection === 'asc')
                            <flux:icon.chevron-up variant="micro" />
                        @else
                            <flux:icon.chevron-down variant="micro" />
                        @endif
                    @endif
                </button>
            </flux:table.column>
            <flux:table.column>Contact</flux:table.column>
            <flux:table.column>Company</flux:table.column>
            <flux:table.column>Interests</flux:table.column>
            <flux:table.column>Planning to Buy</flux:table.column>
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
            @forelse ($visitors as $visitor)
                <flux:table.row :key="$visitor->id">
                    <flux:table.cell>
                        <div class="font-semibold">{{ $visitor->name }}</div>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="text-sm">
                            @if ($visitor->email)
                                <div>{{ $visitor->email }}</div>
                            @endif
                            @if ($visitor->phone)
                                <div class="text-xs text-zinc-500">{{ $visitor->phone }}</div>
                            @endif
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>
                        @if ($visitor->company_name)
                            {{ $visitor->company_name }}
                        @else
                            <span class="text-zinc-400">—</span>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        @if ($visitor->interests && count($visitor->interests) > 0)
                            <div class="flex flex-wrap gap-1">
                                @foreach (array_slice($visitor->interests, 0, 2) as $interest)
                                    <flux:badge size="sm" color="zinc">{{ ucfirst($interest) }}</flux:badge>
                                @endforeach
                                @if (count($visitor->interests) > 2)
                                    <flux:badge size="sm" color="zinc">+{{ count($visitor->interests) - 2 }}
                                    </flux:badge>
                                @endif
                            </div>
                        @else
                            <span class="text-zinc-400">—</span>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        @if ($visitor->planning_to_buy)
                            <flux:badge size="sm" color="green">{{ ucfirst($visitor->planning_to_buy) }}
                            </flux:badge>
                        @else
                            <span class="text-zinc-400">—</span>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        <time datetime="{{ $visitor->created_at->toISOString() }}" class="text-sm">
                            {{ $visitor->created_at->format('M d, Y') }}
                        </time>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="flex items-center gap-2">
                            <flux:dropdown position="bottom" align="end">
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                                <flux:menu class="w-48">
                                    <flux:menu.item icon="eye" icon:variant="outline"
                                        wire:click="$dispatch('show-visitor-details', { visitorId: {{ $visitor->id }} })">
                                        View Details
                                    </flux:menu.item>
                                    <flux:menu.separator />
                                    <flux:menu.item icon="trash" icon:variant="outline" variant="danger"
                                        wire:click="confirmDelete({{ $visitor->id }})">Delete</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="7" class="text-center">
                        <div class="py-12">
                            <flux:icon.user-group class="mx-auto mb-4 text-zinc-400" variant="outline" />
                            <flux:heading size="lg" class="mb-2">No visitors found</flux:heading>
                            <flux:subheading class="mb-4">
                                @if ($search)
                                    Try adjusting your search criteria
                                @else
                                    Visitors will appear here once they register
                                @endif
                            </flux:subheading>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    @if ($visitors->hasPages())
        <div class="border-t border-zinc-200 py-4 dark:border-zinc-700">
            {{ $visitors->links() }}
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    <flux:modal name="delete-visitor" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete visitor?</flux:heading>
                <flux:text class="mt-2">
                    <p>You're about to delete this visitor registration.</p>
                    <p>This action cannot be reversed.</p>
                </flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="cancelDelete">Cancel</flux:button>
                <flux:button variant="danger" wire:click="deleteVisitor">Delete visitor</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
