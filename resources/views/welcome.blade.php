<x-layouts.front :showHeader="false">
    <!-- Hero Banner Section -->
    <section class="w-full overflow-hidden border-b">
        <div class="w-full">

            <a href="{{ route('visitor.register') }}">
                <img src="/banner-web.jpg" alt="{{ config('app.name') }} - Property Exhibition"
                    class="w-full h-auto object-contain" />
            </a>
        </div>
    </section>

    <!-- Main Sponsors Section -->
    <x-main-sponsors :sponsors="$mainSponsors" />

    <!-- Associate Partners Section -->
    <x-associate-partners :partners="$associatePartners" />

    <!-- Co-Partners Section -->
    <x-co-partners :partners="$coPartners" />

    <!-- Other Exhibitors Section -->
    <section id="exhibitors" class="py-16 bg-white border-b border-zinc-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center mb-12">
                <span class="text-xs font-semibold tracking-wider text-teal-600 uppercase">Our Exhibitors</span>
                <h2 class="mt-2 text-2xl sm:text-3xl font-semibold text-zinc-900">
                    Meet the Industry Leaders
                </h2>
                <div class="mt-3 w-16 h-1 bg-teal-600 mx-auto rounded-full"></div>
            </div>

            <!-- Exhibitors Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                @forelse($exhibitors->take(6) as $exhibitor)
                    <a href="{{ route('exhibitor.show', $exhibitor) }}"
                        class="group block h-full bg-white rounded-xl p-6 hover:bg-zinc-50 hover:shadow-lg transition-all duration-300 border border-zinc-200">
                        <!-- Header with Logo and Name -->
                        <div class="flex items-start gap-4 mb-4">
                            <div class="w-14 h-14 flex items-center justify-center flex-shrink-0">
                                @if ($exhibitor->preview_logo ?? $exhibitor->logo_path)
                                    <img src="{{ Storage::url($exhibitor->preview_logo ?? $exhibitor->logo_path) }}"
                                        alt="{{ $exhibitor->brand_name }}" class="w-full h-full object-contain" />
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
                                        class="inline-flex items-center px-2 py-0.5 mt-1 text-xs font-light bg-zinc-200 text-zinc-600 rounded">
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
                        <div class="flex items-center justify-between pt-3 border-t border-zinc-200/50">
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
                @empty
                    <!-- Placeholder cards when no exhibitors -->
                    @for ($i = 0; $i < 3; $i++)
                        <div class="h-full bg-white rounded-xl p-6 border border-zinc-200">
                            <div class="flex items-start gap-4 mb-4">
                                <div
                                    class="w-14 h-14 bg-white rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm border border-zinc-100">
                                    <x-heroicon-o-building-office class="w-6 h-6 text-zinc-300" />
                                </div>
                                <div>
                                    <h3 class="text-base font-normal text-zinc-400">Coming Soon</h3>
                                    <span class="text-xs font-light text-zinc-300">Exhibitor details will be available
                                        soon</span>
                                </div>
                            </div>
                        </div>
                    @endfor
                @endforelse
            </div>

            @if ($exhibitors->count() > 6)
                <!-- View All Button -->
                <div class="text-center">
                    <flux:button href="{{ route('public.exhibitors') }}" variant="primary">
                        View All Exhibitors
                    </flux:button>
                </div>
            @endif
        </div>
    </section>

    <!-- Partners Section -->
    <section class="py-12 bg-white border-y border-zinc-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="text-xs font-light tracking-widest text-zinc-400 uppercase">Our Partners</span>
            </div>

            <!-- Partners Grid -->
            <div class="flex flex-col md:flex-row items-center justify-center gap-12 md:gap-24">
                <!-- Event Partner -->
                <a href="https://www.palevents.in/" target="_blank" rel="noopener noreferrer" class="flex flex-col items-center gap-4 group">
                    <div class="w-48 h-24 flex items-center justify-center transition-transform group-hover:scale-105">
                        <img src="{{ asset('brands/pal.png') }}" alt="Pal Events" class="max-w-full max-h-full object-contain">
                    </div>
                    <span class="text-xs font-light tracking-wide text-zinc-400 uppercase">Event Partner</span>
                </a>

                <!-- IT Partner -->
                <a href="https://techable.in" target="_blank" rel="noopener noreferrer" class="flex flex-col items-center gap-4 group">
                    <div class="w-48 h-24 flex items-center justify-center transition-transform group-hover:scale-105">
                        <img src="{{ asset('brands/techable.png') }}" alt="Techable Consultancy" class="max-w-full max-h-full object-contain">
                    </div>
                    <span class="text-xs font-light tracking-wide text-zinc-400 uppercase">IT Partner</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Registration CTA Section -->
    <section id="register" class="py-20 bg-zinc-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center mb-10">
                <span class="text-xs font-light tracking-widest text-zinc-400 uppercase">Free Registration</span>
                <h2 class="mt-3 text-2xl sm:text-3xl font-light text-zinc-900">
                    Get Your Free Pass
                </h2>
                <p class="mt-3 text-sm text-zinc-600 max-w-xl mx-auto font-light">
                    Register to receive your visitor pass and access all exhibitors
                </p>
            </div>

            <!-- Registration CTA -->
            <div class="max-w-lg mx-auto text-center">
                <div class="bg-white border border-zinc-200 rounded-2xl p-8">
                    <div class="mb-6">
                        <h3 class="text-xl font-light text-zinc-900 mb-2">Quick & Easy Registration</h3>
                        <p class="text-sm text-zinc-600 font-light">
                            Complete your visitor registration in just 2 simple steps
                        </p>
                    </div>

                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <div class="text-center">
                            <div
                                class="w-8 h-8 bg-zinc-200 rounded-full flex items-center justify-center mx-auto mb-2">
                                <span class="text-xs font-medium">1</span>
                            </div>
                            <p class="text-xs text-zinc-600">Personal Info</p>
                        </div>
                        <div class="text-center">
                            <div
                                class="w-8 h-8 bg-zinc-200 rounded-full flex items-center justify-center mx-auto mb-2">
                                <span class="text-xs font-medium">2</span>
                            </div>
                            <p class="text-xs text-zinc-600">Preferences</p>
                        </div>
                        <div class="text-center">
                            <div
                                class="w-8 h-8 bg-teal-600 rounded-full flex items-center justify-center mx-auto mb-2">
                                <span class="text-xs font-medium text-white">
                                    <flux:icon name="check" class="w-4 h-4" />
                                </span>
                            </div>
                            <p class="text-xs text-zinc-600">Get Pass</p>
                        </div>
                    </div>

                    <flux:button variant="primary" color="zinc" href="{{ route('visitor.register') }}"
                        icon="arrow-right" class="w-full">
                        Start Registration
                    </flux:button>
                </div>

                <!-- Additional Info -->
                <div class="mt-6 flex items-center justify-center gap-6 text-xs font-light text-zinc-500">
                    <span>100% Free</span>
                    <span>•</span>
                    <span>Secure</span>
                    <span>•</span>
                    <span>Instant Pass</span>
                </div>
            </div>
        </div>
    </section>
</x-layouts.front>
