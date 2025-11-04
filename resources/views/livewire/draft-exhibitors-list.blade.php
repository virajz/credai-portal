<div class="mx-auto w-full max-w-7xl space-y-6">
    <!-- Header -->
    <div>
        <flux:heading size="xl">Draft Exhibitor Submissions</flux:heading>
        <flux:subheading>Track incomplete and abandoned form submissions</flux:subheading>
    </div>


    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
        <flux:card>
            <div class="text-center">
                <div class="text-3xl font-bold text-zinc-900 dark:text-white">{{ $stats['total'] }}</div>
                <div class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Total Drafts</div>
            </div>
        </flux:card>

        <flux:card>
            <div class="text-center">
                <div class="text-3xl font-bold text-amber-600 dark:text-amber-500">{{ $stats['incomplete'] }}</div>
                <div class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Incomplete</div>
            </div>
        </flux:card>

        <flux:card>
            <div class="text-center">
                <div class="text-3xl font-bold text-green-600 dark:text-green-500">{{ $stats['completed'] }}</div>
                <div class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Completed</div>
            </div>
        </flux:card>

        <flux:card>
            <div class="text-center">
                <div class="text-3xl font-bold text-blue-600 dark:text-blue-500">{{ $stats['active_today'] }}</div>
                <div class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Active Today</div>
            </div>
        </flux:card>
    </div>

    <!-- Filters and Search -->
    <flux:card>
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div class="flex-1">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Search by name, phone, or email..."
                    icon="magnifying-glass" />
            </div>

            <div class="flex gap-2">
                <flux:button variant="{{ $filter === 'all' ? 'primary' : 'ghost' }}" size="sm"
                    wire:click="$set('filter', 'all')">
                    All
                </flux:button>
                <flux:button variant="{{ $filter === 'incomplete' ? 'primary' : 'ghost' }}" size="sm"
                    wire:click="$set('filter', 'incomplete')">
                    Incomplete
                </flux:button>
                <flux:button variant="{{ $filter === 'completed' ? 'primary' : 'ghost' }}" size="sm"
                    wire:click="$set('filter', 'completed')">
                    Completed
                </flux:button>
            </div>
        </div>
    </flux:card>

    <!-- Drafts Table -->
    <flux:card>
        @if ($drafts->isEmpty())
            <div class="py-12 text-center">
                <flux:icon.document-text class="mx-auto size-12 text-zinc-400 dark:text-zinc-600" />
                <flux:heading size="lg" class="mt-4">No drafts found</flux:heading>
                <flux:subheading>
                    @if ($search)
                        Try adjusting your search or filters.
                    @else
                        Draft submissions will appear here as users start filling the form.
                    @endif
                </flux:subheading>
            </div>
        @else
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Contact Details</flux:table.column>
                    <flux:table.column>Company</flux:table.column>
                    <flux:table.column>Resume Link</flux:table.column>
                    <flux:table.column>Progress</flux:table.column>
                    <flux:table.column>Last Activity</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Actions</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($drafts as $draft)
                        <flux:table.row :key="$draft->id">
                            <flux:table.cell>
                                <div>
                                    <div class="font-medium">
                                        {{ $draft->contact_person_name ?: 'Not provided' }}
                                    </div>
                                    @if ($draft->phone_number)
                                        <div class="text-sm text-zinc-600 dark:text-zinc-400">
                                            {{ $draft->phone_number }}
                                        </div>
                                    @endif
                                    @if ($draft->email)
                                        <div class="text-sm text-zinc-600 dark:text-zinc-400">
                                            {{ $draft->email }}
                                        </div>
                                    @endif
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                <div>
                                    <div class="font-medium">
                                        {{ $draft->brand_name ?: '—' }}
                                    </div>
                                    @if ($draft->city)
                                        <div class="text-sm text-zinc-600 dark:text-zinc-400">
                                            {{ $draft->city }}
                                        </div>
                                    @endif
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                <div class="flex items-center gap-2">
                                    <input type="text" readonly value="{{ $draft->resume_url }}"
                                        class="w-64 rounded border border-zinc-300 bg-zinc-50 px-2 py-1 text-xs font-mono dark:border-zinc-700 dark:bg-zinc-800"
                                        onclick="this.select()">
                                    <flux:button size="xs" variant="primary" icon="clipboard" x-data
                                        x-on:click="
                                        navigator.clipboard.writeText('{{ $draft->resume_url }}');
                                        $dispatch('resume-link-copied');
                                    "
                                        title="Copy link">
                                    </flux:button>
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                <div class="flex items-center gap-2">
                                    <div class="text-sm">
                                        Step {{ $draft->current_step }}/4
                                    </div>
                                    <div class="h-2 w-24 overflow-hidden rounded-full bg-zinc-200 dark:bg-zinc-700">
                                        <div class="h-full bg-blue-600 dark:bg-blue-500"
                                            style="width: {{ ($draft->current_step / 4) * 100 }}%"></div>
                                    </div>
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                <div class="text-sm">
                                    {{ $draft->last_activity_at?->diffForHumans() ?? 'Never' }}
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                @if ($draft->is_completed)
                                    <flux:badge color="green" size="sm">Completed</flux:badge>
                                @else
                                    <flux:badge color="amber" size="sm">In Progress</flux:badge>
                                @endif
                            </flux:table.cell>

                            <flux:table.cell>
                                <div class="flex items-center gap-2">
                                    <flux:button size="xs" variant="ghost" icon="trash" color="red"
                                        wire:click="confirmDelete({{ $draft->id }})" title="Delete draft">
                                    </flux:button>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>

            <div class="mt-4">
                {{ $drafts->links() }}
            </div>
        @endif
    </flux:card>

    <!-- Toast notification for copy -->
    <div x-data="{ show: false }" x-on:resume-link-copied.window="show = true; setTimeout(() => show = false, 3000)"
        x-show="show" x-transition
        class="fixed bottom-4 right-4 z-50 rounded-lg bg-green-600 px-4 py-3 text-white shadow-lg dark:bg-green-500"
        style="display: none;">
        <div class="flex items-center gap-2">
            <flux:icon.check class="size-5" />
            <span>Resume link copied to clipboard!</span>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <flux:modal name="delete-draft" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete draft?</flux:heading>
                <flux:text class="mt-2">
                    <p>You're about to delete this draft submission.</p>
                    <p>This action cannot be reversed.</p>
                </flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="cancelDelete">Cancel</flux:button>
                <flux:button variant="danger" wire:click="deleteDraft">Delete draft</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
