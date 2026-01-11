<div>
    <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-6">
        <!-- Profile Views -->
        <button wire:click="showDetails('profile_views', 'Profile Views')" class="text-left transition-transform hover:scale-105">
            <flux:card>
                <div class="text-center">
                    <div class="mb-2 text-3xl font-bold text-blue-600 dark:text-blue-400">
                        {{ number_format($this->summary['profile_views']) }}
                    </div>
                    <div class="text-xs text-zinc-500 dark:text-zinc-400">Profile Views</div>
                </div>
            </flux:card>
        </button>

        <!-- Project Views -->
        <button wire:click="showDetails('project_views', 'Project Views')" class="text-left transition-transform hover:scale-105">
            <flux:card>
                <div class="text-center">
                    <div class="mb-2 text-3xl font-bold text-purple-600 dark:text-purple-400">
                        {{ number_format($this->summary['project_views']) }}
                    </div>
                    <div class="text-xs text-zinc-500 dark:text-zinc-400">Project Views</div>
                </div>
            </flux:card>
        </button>

        <!-- Brochure Downloads -->
        <button wire:click="showDetails('downloads', 'Downloads')" class="text-left transition-transform hover:scale-105">
            <flux:card>
                <div class="text-center">
                    <div class="mb-2 text-3xl font-bold text-teal-600 dark:text-teal-400">
                        {{ number_format($this->summary['brochure_downloads'] + $this->summary['project_brochure_downloads']) }}
                    </div>
                    <div class="text-xs text-zinc-500 dark:text-zinc-400">Downloads</div>
                </div>
            </flux:card>
        </button>

        <!-- Calls -->
        <button wire:click="showDetails('calls', 'Calls')" class="text-left transition-transform hover:scale-105">
            <flux:card>
                <div class="text-center">
                    <div class="mb-2 text-3xl font-bold text-amber-600 dark:text-amber-400">
                        {{ number_format($this->summary['calls']) }}
                    </div>
                    <div class="text-xs text-zinc-500 dark:text-zinc-400">Calls</div>
                </div>
            </flux:card>
        </button>

        <!-- Website Visits -->
        <button wire:click="showDetails('website_visits', 'Website Visits')" class="text-left transition-transform hover:scale-105">
            <flux:card>
                <div class="text-center">
                    <div class="mb-2 text-3xl font-bold text-rose-600 dark:text-rose-400">
                        {{ number_format($this->summary['website_visits']) }}
                    </div>
                    <div class="text-xs text-zinc-500 dark:text-zinc-400">Website Visits</div>
                </div>
            </flux:card>
        </button>

        <!-- QR Scans -->
        <button wire:click="showDetails('qr_scans', 'QR Scans')" class="text-left transition-transform hover:scale-105">
            <flux:card>
                <div class="text-center">
                    <div class="mb-2 text-3xl font-bold text-green-600 dark:text-green-400">
                        {{ number_format($this->summary['qr_scans']) }}
                    </div>
                    <div class="text-xs text-zinc-500 dark:text-zinc-400">QR Scans</div>
                </div>
            </flux:card>
        </button>
    </div>

    <!-- Detail Modal -->
    <flux:modal name="analytics-detail" variant="flyout" wire:model="showModal" class="space-y-6">
        <div>
            <flux:heading size="lg">{{ $selectedMetricLabel }} by Company</flux:heading>
            <flux:subheading class="mt-2">Companies ranked by highest {{ strtolower($selectedMetricLabel) }}</flux:subheading>
        </div>

        @if ($this->detailData && $this->detailData->isNotEmpty())
            <div class="space-y-4">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Rank</flux:table.column>
                        <flux:table.column>Company Name</flux:table.column>
                        <flux:table.column>Count</flux:table.column>
                        <flux:table.column>Actions</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach ($this->detailData as $index => $company)
                            <flux:table.row :key="$company->id">
                                <flux:table.cell>
                                    <flux:badge variant="outline">
                                        #{{ $this->detailData->firstItem() + $index }}
                                    </flux:badge>
                                </flux:table.cell>
                                <flux:table.cell>
                                    <div class="font-medium text-zinc-900 dark:text-white">
                                        {{ $company->company_name }}
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell>
                                    <div class="text-lg font-bold text-zinc-900 dark:text-white">
                                        {{ number_format($company->event_count) }}
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell>
                                    <flux:button href="{{ route('companies.analytics', $company) }}" variant="ghost" size="sm" icon="arrow-right">
                                        View Details
                                    </flux:button>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>

                <div class="mt-4">
                    {{ $this->detailData->links() }}
                </div>
            </div>
        @else
            <div class="py-12 text-center">
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800">
                    <flux:icon.chart-bar class="h-6 w-6 text-zinc-400" />
                </div>
                <flux:heading size="lg" class="mb-2">No data available</flux:heading>
                <flux:subheading>No companies have {{ strtolower($selectedMetricLabel) }} yet</flux:subheading>
            </div>
        @endif

        <div class="flex">
            <flux:spacer />
            <flux:button wire:click="closeModal" variant="ghost">Close</flux:button>
        </div>
    </flux:modal>
</div>
