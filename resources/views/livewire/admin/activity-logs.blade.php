<div class="mx-auto w-full">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl" class="mb-2">Activity Logs</flux:heading>
            <flux:subheading>Track all system activities and user actions</flux:subheading>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end">
        <div class="flex-1">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Search logs..." icon="magnifying-glass" />
        </div>

        <div class="flex gap-2">
            <flux:select wire:model.live="filterAction" placeholder="All Actions" class="w-48">
                <option value="">All Actions</option>
                @foreach ($actions as $action)
                    <option value="{{ $action }}">{{ $action }}</option>
                @endforeach
            </flux:select>

            @if ($this->hasActiveFilters())
                <flux:button variant="ghost" icon="x-mark" wire:click="clearFilters">
                    Clear All
                </flux:button>
            @endif
        </div>
    </div>

    <!-- Activity Logs Table -->
    <flux:table>
        <flux:table.columns>
            <flux:table.column>Timestamp</flux:table.column>
            <flux:table.column>User</flux:table.column>
            <flux:table.column>Action</flux:table.column>
            <flux:table.column>Description</flux:table.column>
            <flux:table.column>IP Address</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($logs as $log)
                <flux:table.row :key="$log->id">
                    <flux:table.cell>
                        <time datetime="{{ $log->created_at->toISOString() }}" class="text-sm">
                            {{ $log->created_at->format('M d, Y H:i:s') }}
                        </time>
                    </flux:table.cell>
                    <flux:table.cell>
                        @if ($log->user)
                            <div class="font-medium">{{ $log->user->name }}</div>
                            <div class="text-xs text-zinc-500">{{ $log->user->email }}</div>
                        @else
                            <span class="text-zinc-400">System</span>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm"
                            :color="match ($log->action) {
                                'user_created' => 'green',
                                'user_updated' => 'blue',
                                'user_deleted' => 'red',
                                'company_created' => 'green',
                                'company_updated' => 'blue',
                                'company_deleted' => 'red',
                                'company_locked' => 'orange',
                                'company_unlocked' => 'lime',
                                'registration_link_generated' => 'sky',
                                'visitor_registered' => 'emerald',
                                default => 'purple',
                            }">
                            {{ $log->action }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ $log->description ?? '-' }}
                    </flux:table.cell>
                    <flux:table.cell>
                        <span class="font-mono text-xs">{{ $log->ip_address ?? '-' }}</span>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="5" class="text-center">
                        <div class="py-12">
                            <flux:icon.inbox class="mx-auto mb-4 text-zinc-400" variant="outline" />
                            <flux:heading size="lg" class="mb-2">No activity logs found</flux:heading>
                            <flux:subheading class="mb-4">
                                @if ($search)
                                    Try adjusting your search criteria
                                @else
                                    No activity has been logged yet
                                @endif
                            </flux:subheading>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    @if ($logs->hasPages())
        <div class="border-t border-zinc-200 py-4 dark:border-zinc-700">
            {{ $logs->links() }}
        </div>
    @endif
</div>
