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
                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($projects as $project)
                            <a href="{{ route('project.show', $project) }}"
                                class="group overflow-hidden rounded-lg border border-zinc-200 bg-white transition-all hover:shadow-lg dark:border-zinc-800 dark:bg-zinc-900">
                                <!-- Project Image/Logo -->
                                <div class="aspect-video bg-white dark:bg-zinc-800">
                                    @if ($project->logo_path)
                                        <img src="{{ Storage::url($project->logo_path) }}" alt="{{ $project->name }}"
                                            class="h-full w-full object-contain p-4">
                                    @else
                                        <div class="flex h-full items-center justify-center">
                                            <x-heroicon-o-building-office-2
                                                class="h-16 w-16 text-zinc-300 dark:text-zinc-600" />
                                        </div>
                                    @endif
                                </div>

                                <!-- Project Details -->
                                <div class="p-4">
                                    <div class="flex items-start justify-between gap-2">
                                        <flux:heading size="base" class="group-hover:opacity-75">
                                            {{ $project->name }}
                                        </flux:heading>
                                        @if ($project->status)
                                            <flux:badge size="sm" color="zinc">
                                                {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                            </flux:badge>
                                        @endif
                                    </div>

                                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                                        {{ $project->exhibitor?->brand_name ?? 'N/A' }}
                                    </p>

                                    <div class="mt-3 space-y-2">
                                        @if ($project->area)
                                            <div
                                                class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                                                <x-heroicon-o-map-pin class="w-3.5 h-3.5" />
                                                {{ $project->area }}
                                            </div>
                                        @endif

                                        @if ($project->category)
                                            <div
                                                class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                                                <x-heroicon-o-building-office-2 class="w-3.5 h-3.5" />
                                                {{ $project->category }}
                                            </div>
                                        @endif

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

                                        @if (!empty($allBedrooms))
                                            <div
                                                class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                                                <x-heroicon-o-home class="w-3.5 h-3.5" />
                                                {{ implode(', ', $allBedrooms) }}
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
