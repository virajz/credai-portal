<div>
    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-zinc-800 to-zinc-900 pt-8 pb-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Page Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-light text-white mb-3">
                    Discover <span class="font-normal">Exhibitors</span>
                </h1>
                <p class="text-base text-white/70 max-w-2xl mx-auto font-light">
                    Browse our collection of trusted developers and find your perfect property
                </p>
            </div>

            <!-- Filters Section -->
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div>
                        <flux:input wire:model.live.debounce.300ms="search" placeholder="Search exhibitors..."
                            icon="magnifying-glass" />
                    </div>

                    <!-- Property Type -->
                    <div>
                        <flux:select variant="listbox" wire:model.live="propertyType" placeholder="Property Type"
                            multiple>
                            <flux:select.option value="Residential">Residential</flux:select.option>
                            <flux:select.option value="Commercial">Commercial</flux:select.option>
                            <flux:select.option value="Plotting">Plotting</flux:select.option>
                        </flux:select>
                    </div>

                    <!-- Location -->
                    <div>
                        <flux:select variant="listbox" wire:model.live="location" placeholder="Location" multiple>
                            @foreach ($availableLocations as $loc)
                                <flux:select.option value="{{ $loc }}">{{ $loc }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>

                    <!-- Price Range -->
                    <div>
                        <flux:select variant="listbox" wire:model.live="priceRange" placeholder="Price Range" multiple>
                            @foreach ($availablePriceRanges as $range)
                                <flux:select.option value="{{ $range }}">{{ $range }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                </div>

                <!-- Active Filters & Clear Button -->
                @if ($search || ! empty($propertyType) || ! empty($location) || ! empty($priceRange))
                    <div class="flex items-center justify-between mt-4 pt-4 border-t border-white/10">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs font-light text-white/60">Active filters:</span>
                            @if ($search)
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-light bg-white/10 text-white rounded-full">
                                    Search: {{ $search }}
                                    <button wire:click="$set('search', '')" class="hover:text-white/80">
                                        <x-heroicon-o-x-mark class="w-3 h-3" />
                                    </button>
                                </span>
                            @endif
                            @foreach ($propertyType as $type)
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-light bg-white/10 text-white rounded-full">
                                    {{ $type }}
                                    <button wire:click="$set('propertyType', {{ json_encode(array_values(array_diff($propertyType, [$type]))) }})"
                                        class="hover:text-white/80">
                                        <x-heroicon-o-x-mark class="w-3 h-3" />
                                    </button>
                                </span>
                            @endforeach
                            @foreach ($location as $loc)
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-light bg-white/10 text-white rounded-full">
                                    {{ $loc }}
                                    <button wire:click="$set('location', {{ json_encode(array_values(array_diff($location, [$loc]))) }})"
                                        class="hover:text-white/80">
                                        <x-heroicon-o-x-mark class="w-3 h-3" />
                                    </button>
                                </span>
                            @endforeach
                            @foreach ($priceRange as $price)
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-light bg-white/10 text-white rounded-full">
                                    {{ $price }}
                                    <button wire:click="$set('priceRange', {{ json_encode(array_values(array_diff($priceRange, [$price]))) }})"
                                        class="hover:text-white/80">
                                        <x-heroicon-o-x-mark class="w-3 h-3" />
                                    </button>
                                </span>
                            @endforeach
                        </div>
                        <flux:button wire:click="clearFilters" size="sm" variant="ghost"
                            class="text-white/60 hover:text-white">
                            Clear All
                        </flux:button>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Exhibitors Grid Section -->
    <section class="py-12 bg-zinc-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Results Count -->
            <div class="flex items-center justify-between mb-6">
                <p class="text-sm font-light text-zinc-600">
                    @if ($exhibitors->total() === 0)
                        No exhibitors found
                    @elseif ($exhibitors->total() === 1)
                        Showing 1 exhibitor
                    @else
                        Showing {{ $exhibitors->firstItem() }}-{{ $exhibitors->lastItem() }} of
                        {{ $exhibitors->total() }} exhibitors
                    @endif
                </p>
            </div>

            @if ($exhibitors->count() > 0)
                <!-- Exhibitors Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    @foreach ($exhibitors as $exhibitor)
                        <a href="{{ route('exhibitor.show', $exhibitor) }}"
                            class="group block h-full bg-white rounded-xl p-6 hover:shadow-lg transition-all duration-300">
                            <!-- Header with Logo and Name -->
                            <div class="flex items-start gap-4 mb-4">
                                <div
                                    class="w-14 h-14 bg-zinc-50 rounded-xl flex items-center justify-center flex-shrink-0 border border-zinc-100 group-hover:border-zinc-200 transition-colors p-2">
                                    @if ($exhibitor->preview_logo ?? $exhibitor->logo_path)
                                        <img src="{{ Storage::url($exhibitor->preview_logo ?? $exhibitor->logo_path) }}"
                                            alt="{{ $exhibitor->brand_name }}"
                                            class="w-full h-full object-contain rounded" />
                                    @else
                                        <x-heroicon-o-building-office class="w-6 h-6 text-zinc-400" />
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-base font-normal text-zinc-900 group-hover:text-zinc-700 truncate">
                                        {{ $exhibitor->brand_name }}
                                    </h3>
                                    @if ($exhibitor->company?->stall_number)
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 mt-1 text-xs font-light bg-zinc-100 text-zinc-600 rounded">
                                            Stall {{ $exhibitor->company->stall_number }}
                                        </span>
                                    @endif
                                </div>
                                <x-heroicon-o-chevron-right
                                    class="w-5 h-5 text-zinc-300 group-hover:text-zinc-500 group-hover:translate-x-1 transition-all flex-shrink-0" />
                            </div>

                            <!-- Contact Info -->
                            <div class="space-y-2 mb-4">
                                @if ($exhibitor->city)
                                    <div class="flex items-center gap-2 text-xs text-zinc-500">
                                        <x-heroicon-o-map-pin class="w-3.5 h-3.5 text-zinc-400" />
                                        <span class="font-light">{{ $exhibitor->city }}</span>
                                    </div>
                                @endif
                                @if ($exhibitor->phone_number)
                                    <div class="flex items-center gap-2 text-xs text-zinc-500">
                                        <x-heroicon-o-phone class="w-3.5 h-3.5 text-zinc-400" />
                                        <span class="font-light">{{ $exhibitor->phone_number }}</span>
                                    </div>
                                @endif
                                @if ($exhibitor->website)
                                    <div class="flex items-center gap-2 text-xs text-zinc-500">
                                        <x-heroicon-o-globe-alt class="w-3.5 h-3.5 text-zinc-400" />
                                        <span
                                            class="font-light truncate">{{ parse_url($exhibitor->website, PHP_URL_HOST) ?? $exhibitor->website }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Footer with Projects -->
                            <div class="flex items-center justify-between pt-3 border-t border-zinc-100">
                                @if ($exhibitor->projects->count() > 0)
                                    <span class="text-xs font-light text-zinc-500">
                                        {{ $exhibitor->projects->count() }}
                                        {{ Str::plural('Project', $exhibitor->projects->count()) }}
                                    </span>
                                @else
                                    <span class="text-xs font-light text-zinc-400">View Details</span>
                                @endif
                                <span class="text-xs font-light text-zinc-400 group-hover:text-zinc-600">Learn more
                                    &rarr;</span>
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $exhibitors->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-xl p-12 text-center">
                    <div class="w-16 h-16 bg-zinc-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <x-heroicon-o-funnel class="w-8 h-8 text-zinc-400" />
                    </div>
                    <h3 class="text-lg font-normal text-zinc-900 mb-2">No exhibitors found</h3>
                    <p class="text-sm font-light text-zinc-500 mb-6 max-w-md mx-auto">
                        We couldn't find any exhibitors matching your search criteria. Try adjusting your filters.
                    </p>
                    <flux:button wire:click="clearFilters" variant="primary">
                        Clear All Filters
                    </flux:button>
                </div>
            @endif
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-2xl sm:text-3xl font-light text-zinc-900 mb-3">
                Ready to Visit?
            </h2>
            <p class="text-sm text-zinc-500 font-light mb-8 max-w-xl mx-auto">
                Register now to get your free visitor pass and explore all exhibitors
            </p>
            <a href="{{ route('visitor.register') }}"
                class="inline-flex items-center px-6 py-3 text-sm font-normal text-white bg-zinc-900 hover:bg-zinc-800 rounded-full transition-colors">
                Get Your Free Pass
            </a>
        </div>
    </section>
</div>
