<div class="mx-auto w-full max-w-6xl space-y-6">
    <!-- Header with Filters -->
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">Analytics</flux:heading>
            <flux:subheading>{{ $company->company_name }}</flux:subheading>
        </div>
        <flux:button href="{{ route('companies.edit', $company) }}" variant="ghost" icon="arrow-left">
            Back to Company
        </flux:button>
    </div>

    @if ($company->exhibitor)
        <!-- Filters -->
        <flux:card>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <flux:select wire:model.live="dateRange" label="Date Range" variant="listbox">
                    <flux:select.option value="1">Last 24 hours</flux:select.option>
                    <flux:select.option value="7">Last 7 days</flux:select.option>
                    <flux:select.option value="30">Last 30 days</flux:select.option>
                    <flux:select.option value="90">Last 90 days</flux:select.option>
                    <flux:select.option value="all">All time</flux:select.option>
                </flux:select>

                <flux:select wire:model.live="eventTypeFilter" label="Event Type" variant="listbox">
                    <flux:select.option value="all">All Events</flux:select.option>
                    <flux:select.option value="exhibitor_profile_view">Profile Views</flux:select.option>
                    <flux:select.option value="project_view">Project Views</flux:select.option>
                    <flux:select.option value="company_brochure_download">Brochure Downloads</flux:select.option>
                    <flux:select.option value="project_brochure_download">Project Brochure Downloads
                    </flux:select.option>
                    <flux:select.option value="call_clicked">Calls</flux:select.option>
                    <flux:select.option value="website_visit_clicked">Website Visits</flux:select.option>
                    <flux:select.option value="qr_scanned">QR Scans</flux:select.option>
                </flux:select>
            </div>
        </flux:card>

        <!-- Summary Stats -->
        <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-6">
            <!-- Total Events -->
            {{-- <flux:card>
                <div class="text-center">
                    <div class="mb-2 text-3xl font-bold text-zinc-900 dark:text-white">
                        {{ number_format($summary['total_events']) }}
                    </div>
                    <div class="text-xs text-zinc-500 dark:text-zinc-400">Total Events</div>
                </div>
            </flux:card> --}}

            <!-- Profile Views -->
            <flux:card>
                <div class="text-center">
                    <div class="mb-2 text-3xl font-bold text-blue-600 dark:text-blue-400">
                        {{ number_format($summary['profile_views']) }}
                    </div>
                    <div class="text-xs text-zinc-500 dark:text-zinc-400">Profile Views</div>
                </div>
            </flux:card>

            <!-- Project Views -->
            <flux:card>
                <div class="text-center">
                    <div class="mb-2 text-3xl font-bold text-purple-600 dark:text-purple-400">
                        {{ number_format($summary['project_views']) }}
                    </div>
                    <div class="text-xs text-zinc-500 dark:text-zinc-400">Project Views</div>
                </div>
            </flux:card>

            <!-- Brochure Downloads -->
            <flux:card>
                <div class="text-center">
                    <div class="mb-2 text-3xl font-bold text-teal-600 dark:text-teal-400">
                        {{ number_format($summary['brochure_downloads'] + $summary['project_brochure_downloads']) }}
                    </div>
                    <div class="text-xs text-zinc-500 dark:text-zinc-400">Downloads</div>
                </div>
            </flux:card>

            <!-- Calls -->
            <flux:card>
                <div class="text-center">
                    <div class="mb-2 text-3xl font-bold text-amber-600 dark:text-amber-400">
                        {{ number_format($summary['calls']) }}
                    </div>
                    <div class="text-xs text-zinc-500 dark:text-zinc-400">Calls</div>
                </div>
            </flux:card>

            <!-- Website Visits -->
            <flux:card>
                <div class="text-center">
                    <div class="mb-2 text-3xl font-bold text-rose-600 dark:text-rose-400">
                        {{ number_format($summary['website_visits']) }}
                    </div>
                    <div class="text-xs text-zinc-500 dark:text-zinc-400">Website Visits</div>
                </div>
            </flux:card>

            <!-- QR Scans -->
            <flux:card>
                <div class="text-center">
                    <div class="mb-2 text-3xl font-bold text-green-600 dark:text-green-400">
                        {{ number_format($summary['qr_scans']) }}
                    </div>
                    <div class="text-xs text-zinc-500 dark:text-zinc-400">QR Scans</div>
                </div>
            </flux:card>
        </div>

        <!-- Events Table -->
        <flux:card>
            <flux:heading size="lg" class="mb-4">Recent Events</flux:heading>

            @if ($events->count() > 0)
                <div class="overflow-x-auto">
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>Event Type</flux:table.column>
                            <flux:table.column>Target</flux:table.column>
                            <flux:table.column>IP Address</flux:table.column>
                            <flux:table.column>Date & Time</flux:table.column>
                        </flux:table.columns>

                        <flux:table.rows>
                            @foreach ($events as $event)
                                <flux:table.row :key="$event->id">
                                    <flux:table.cell>
                                        <flux:badge
                                            :color="match ($event->event_type) {
                                                                                                                                                                                                                                                                            'exhibitor_profile_view' => 'blue',
                                                                                                                                                                                                                                                                            'project_view' => 'purple',
                                                                                                                                                                                                                                                                            'company_brochure_download', 'project_brochure_download' => 'teal',
                                                                                                                                                                                                                                                                            'call_clicked' => 'amber',
                                                                                                                                                                                                                                                                            'website_visit_clicked' => 'rose',
                                                                                                                                                                                                                                                                            'qr_scanned' => 'green',
                                                                                                                                                                                                                                                                            default => 'zinc',
                                                                                                                                                                                                                                                                        }">
                                            {{ str_replace('_', ' ', ucfirst($event->event_type)) }}
                                        </flux:badge>
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        @if ($event->trackable)
                                            <div class="text-sm">
                                                <div class="font-medium text-zinc-900 dark:text-white">
                                                    {{ $event->trackable instanceof \App\Models\Exhibitor ? 'Exhibitor Profile' : $event->trackable->name }}
                                                </div>
                                                @if ($event->trackable instanceof \App\Models\Project)
                                                    <div class="text-xs text-zinc-500">
                                                        {{ $event->trackable->exhibitor->company->company_name }}
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-sm text-zinc-400">N/A</span>
                                        @endif
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        <span class="text-sm text-zinc-600 dark:text-zinc-400">
                                            {{ $event->ip_address ?? 'N/A' }}
                                        </span>
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        <div class="text-sm">
                                            <div class="text-zinc-900 dark:text-white">
                                                {{ $event->created_at->format('M d, Y') }}
                                            </div>
                                            <div class="text-xs text-zinc-500">
                                                {{ $event->created_at->format('h:i A') }}
                                            </div>
                                        </div>
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $events->links() }}
                </div>
            @else
                <div class="py-12 text-center">
                    <div
                        class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800">
                        <flux:icon.chart-bar class="h-6 w-6 text-zinc-400" />
                    </div>
                    <flux:heading size="lg" class="mb-2">No analytics data yet</flux:heading>
                    <flux:subheading>Analytics events will appear here once visitors interact with your profile
                    </flux:subheading>
                </div>
            @endif
        </flux:card>
    @else
        <flux:card>
            <div class="py-12 text-center">
                <div
                    class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800">
                    <flux:icon.exclamation-triangle class="h-6 w-6 text-zinc-400" />
                </div>
                <flux:heading size="lg" class="mb-2">No exhibitor data</flux:heading>
                <flux:subheading>This company hasn't created an exhibitor profile yet</flux:subheading>
            </div>
        </flux:card>
    @endif
</div>
