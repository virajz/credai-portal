<div class="min-h-screen">
    <!-- Header -->
    <div>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="py-8">
                <flux:heading size="xl">Browse Projects</flux:heading>
                <flux:subheading class="mt-2">
                    Explore {{ $projects->total() }} properties across various categories and locations.
                </flux:subheading>
            </div>
        </div>
    </div>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="grid gap-8 lg:grid-cols-4">
            <!-- Filters Sidebar -->
            <div class="lg:col-span-1">
                <div class="sticky top-24 max-h-[calc(100vh-7rem)] overflow-y-auto">
                    <flux:card>
                        <div class="space-y-6">
                            <div class="flex items-center justify-between">
                                <flux:heading size="lg">Filters</flux:heading>
                                @if ($category || $area || $bedrooms || $status || $budget || $searchQuery)
                                    <flux:button variant="ghost" size="sm" wire:click="clearFilters"
                                        icon="x-mark">
                                        Clear All
                                    </flux:button>
                                @endif
                            </div>

                            <!-- Search Query Display -->
                            @if ($searchQuery)
                                <flux:badge color="zinc" size="lg" class="w-full">
                                    <div class="w-full">
                                        <div class="text-xs opacity-75">Search:</div>
                                        <div class="mt-0.5">{{ $searchQuery }}</div>
                                    </div>
                                </flux:badge>
                            @endif

                            <!-- Category Filter -->
                            <flux:field>
                                <flux:label>Category</flux:label>
                                <flux:select wire:model.live="category" placeholder="All Categories" variant="listbox">
                                    <flux:select.option value="">All Categories</flux:select.option>
                                    @foreach ($categories as $cat)
                                        <flux:select.option value="{{ $cat }}">{{ $cat }}
                                        </flux:select.option>
                                    @endforeach
                                </flux:select>
                            </flux:field>

                            <!-- Area Filter -->
                            <flux:field>
                                <flux:label>Area</flux:label>
                                <flux:select wire:model.live="area" placeholder="All Areas" variant="listbox">
                                    <flux:select.option value="">All Areas</flux:select.option>
                                    @foreach ($areas as $a)
                                        <flux:select.option value="{{ $a }}">{{ $a }}
                                        </flux:select.option>
                                    @endforeach
                                </flux:select>
                            </flux:field>

                            <!-- Bedrooms Filter -->
                            <flux:field>
                                <flux:label>Bedrooms</flux:label>
                                <flux:select wire:model.live="bedrooms" placeholder="Any" variant="listbox">
                                    <flux:select.option value="">Any</flux:select.option>
                                    @foreach ($bedroomOptions as $option)
                                        <flux:select.option value="{{ $option }}">{{ $option }}
                                        </flux:select.option>
                                    @endforeach
                                </flux:select>
                            </flux:field>

                            <!-- Status Filter -->
                            <flux:field>
                                <flux:label>Status</flux:label>
                                <flux:select wire:model.live="status" placeholder="All Status" variant="listbox">
                                    <flux:select.option value="">All Status</flux:select.option>
                                    @foreach ($statuses as $key => $label)
                                        <flux:select.option value="{{ $key }}">{{ $label }}
                                        </flux:select.option>
                                    @endforeach
                                </flux:select>
                            </flux:field>

                            <!-- Budget Filter -->
                            <flux:field>
                                <flux:label>Budget</flux:label>
                                <flux:input wire:model.live.debounce.500ms="budget" placeholder="e.g., 50L, 2Cr" />
                            </flux:field>
                        </div>
                    </flux:card>
                </div>
            </div>

            <!-- Projects Grid -->
            <div class="lg:col-span-3">
                @if ($projects->isEmpty())
                    <flux:card class="text-center py-12">
                        <div class="flex flex-col items-center">
                            <flux:icon.building-office-2 class="mb-4" variant="outline" size="xl" />
                            <flux:heading size="lg" class="mb-2">No projects found</flux:heading>
                            <flux:subheading class="mb-4">
                                Try adjusting your filters or search criteria
                            </flux:subheading>
                            @if ($category || $area || $bedrooms || $status || $budget)
                                <flux:button wire:click="clearFilters" variant="primary">
                                    Clear Filters
                                </flux:button>
                            @endif
                        </div>
                    </flux:card>
                @else
                    <div class="grid gap-6 sm:grid-cols-1 lg:grid-cols-2">
                        @foreach ($projects as $project)
                            <a href="{{ route('project.show', $project) }}"
                                class="group overflow-hidden rounded-lg border border-zinc-200 bg-white transition-all hover:shadow-lg dark:border-zinc-800 dark:bg-zinc-900">
                                <!-- Project Image/Logo -->
                                <div class="relative aspect-video bg-white dark:bg-zinc-800">
                                    @if ($project->logo_path)
                                        <img src="{{ Storage::url($project->logo_path) }}" alt="{{ $project->name }}"
                                            class="h-full w-full object-contain p-4">
                                    @elseif ($project->exhibitor?->logo_path || $project->exhibitor?->preview_logo)
                                        <img src="{{ Storage::url($project->exhibitor->preview_logo ?? $project->exhibitor->logo_path) }}"
                                            alt="{{ $project->exhibitor->brand_name }}"
                                            class="h-full w-full object-contain p-4">
                                    @else
                                        <div class="flex h-full items-center justify-center">
                                            <x-heroicon-o-building-office-2
                                                class="h-16 w-16 text-zinc-300 dark:text-zinc-600" />
                                        </div>
                                    @endif

                                    @if ($project->status)
                                        <div class="absolute top-3 right-3">
                                            <span
                                                class="inline-flex items-center rounded-full bg-zinc-900/70 backdrop-blur-sm px-2.5 py-1 text-[9px] font-medium text-white dark:bg-zinc-100/80 dark:text-zinc-900">
                                                {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Project Details -->
                                <div class="p-5">
                                    <h3
                                        class="text-lg font-semibold text-zinc-900 group-hover:text-zinc-700 dark:text-white dark:group-hover:text-zinc-300">
                                        {{ $project->name }}
                                    </h3>

                                    <div class="mt-2 flex items-center gap-2">
                                        @if ($project->exhibitor?->logo_path || $project->exhibitor?->preview_logo)
                                            <img src="{{ Storage::url($project->exhibitor->preview_logo ?? $project->exhibitor->logo_path) }}"
                                                alt="{{ $project->exhibitor->brand_name }}"
                                                class="h-4 w-4 object-contain">
                                        @endif
                                        <p class="text-xs font-medium text-zinc-500 dark:text-zinc-500">
                                            <span class="text-normal text-zinc-400">
                                                by
                                            </span>
                                            {{ $project->exhibitor?->brand_name ?? 'N/A' }}
                                        </p>
                                    </div>

                                    @php
                                        $hasUnits = !empty($project->units) && is_array($project->units);
                                        $allBedrooms = [];

                                        if ($hasUnits && count($project->units) > 0) {
                                            foreach ($project->units as $unit) {
                                                if (!empty($unit['bedrooms'])) {
                                                    $allBedrooms[] = $unit['bedrooms'];
                                                }
                                            }
                                            $allBedrooms = array_unique($allBedrooms);
                                            sort($allBedrooms);
                                        }
                                    @endphp

                                    <div
                                        class="mt-4 flex flex-wrap justify-between items-center gap-x-4 gap-y-2 text-xs text-zinc-500 dark:text-zinc-500">
                                        @if ($project->area)
                                            <div class="flex items-center gap-1.5">
                                                <x-heroicon-o-map-pin class="w-3.5 h-3.5 flex-shrink-0" />
                                                <span>{{ $project->area }}</span>
                                            </div>
                                        @endif


                                        @if ($project->category)
                                            <flux:separator vertical />
                                            <div class="flex items-center gap-1.5">
                                                <x-heroicon-o-building-office-2 class="w-3.5 h-3.5 flex-shrink-0" />
                                                <span>{{ $project->category }}</span>
                                            </div>
                                        @endif

                                        @if (!empty($allBedrooms))
                                            <flux:separator vertical />
                                            <div class="flex items-center gap-1.5">
                                                <x-heroicon-o-home class="w-3.5 h-3.5 flex-shrink-0" />
                                                <span>{{ implode(', ', $allBedrooms) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $projects->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
