<x-layouts.front>
    <!-- Hero Section -->
    <section class="relative min-h-screen flex items-center justify-center overflow-hidden">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80"
                alt="Modern Architecture Background" class="w-full h-full object-cover object-center" />
            <div class="absolute inset-0 bg-gradient-to-br from-zinc-900/85 via-zinc-800/75 to-zinc-900/85"></div>
        </div>

        <!-- Background Pattern -->
        <div
            class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff08_1px,transparent_1px),linear-gradient(to_bottom,#ffffff08_1px,transparent_1px)] bg-[size:32px_32px] opacity-30">
        </div>

        <!-- Content -->
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 lg:pt-32 pb-16">
            <div class="text-center">
                <!-- Event Badge -->
                <div
                    class="inline-flex items-center px-3 py-1.5 rounded-full bg-white/10 border border-white/20 backdrop-blur-sm mb-8">
                    <div class="w-1.5 h-1.5 bg-white rounded-full mr-2"></div>
                    <span class="text-xs font-light tracking-wide text-white/90">Property Exhibition</span>
                </div>

                <!-- Logo -->
                <div class="mb-8">
                    <img src="/logo.png" alt="{{ config('app.name') }}" class="h-16 lg:h-20 w-auto mx-auto" />
                </div>

                <!-- Main Headline -->
                <div class="space-y-4 mb-10">
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-light tracking-tight text-white">
                        Discover Your
                        <span class="font-normal">Dream Property</span>
                    </h1>

                    <p class="text-base lg:text-lg text-white/70 max-w-2xl mx-auto leading-relaxed font-light">
                        Explore premium properties from trusted developers,
                        connect with industry experts, and find your perfect investment.
                    </p>
                </div>

                <!-- Location & Venue -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-10">
                    <div class="flex items-center gap-2 text-white/60">
                        <x-heroicon-o-map-pin class="w-4 h-4" />
                        <span class="text-sm font-light">Vanita Vishram Ground, Surat</span>
                    </div>

                    <span class="hidden sm:block text-white/30">•</span>

                    <div class="flex items-center gap-2 text-white/60">
                        <x-heroicon-o-calendar class="w-4 h-4" />
                        <span class="text-sm font-light">January 9-11, 2026</span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 justify-center max-w-md sm:max-w-none mx-auto">
                    <a href="{{ route('visitor.register') }}"
                        class="inline-flex items-center justify-center px-6 py-3 text-sm font-normal text-zinc-900 bg-white hover:bg-zinc-100 rounded-full transition-colors">
                        Get Your Free Pass
                    </a>

                    <a href="#exhibitors"
                        class="inline-flex items-center justify-center px-6 py-3 text-sm font-light text-white border border-white/30 hover:bg-white/10 rounded-full transition-colors">
                        View Exhibitors
                    </a>
                </div>

                <!-- Trust Indicators -->
                <div class="mt-14 flex flex-wrap justify-center gap-8 lg:gap-12">
                    <div class="text-center">
                        <div class="text-xl font-normal text-white">50+</div>
                        <div class="text-xs font-light text-white/50">Developers</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xl font-normal text-white">200+</div>
                        <div class="text-xs font-light text-white/50">Properties</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xl font-normal text-white">₹100Cr+</div>
                        <div class="text-xs font-light text-white/50">Portfolio</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xl font-normal text-white">Free</div>
                        <div class="text-xs font-light text-white/50">Entry</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2">
            <div class="animate-bounce">
                <x-heroicon-o-arrow-down class="w-6 h-6 text-white/60" />
            </div>
        </div>
    </section>

    <!-- Exhibitors Section -->
    <section id="exhibitors" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center mb-12">
                <span class="text-xs font-light tracking-widest text-zinc-400 uppercase">Our Exhibitors</span>
                <h2 class="mt-3 text-2xl sm:text-3xl font-light text-zinc-900">
                    Meet the Industry Leaders
                </h2>
                <p class="mt-3 text-sm text-zinc-500 max-w-xl mx-auto font-light">
                    Connect with trusted developers and real estate professionals
                </p>
            </div>

            <!-- Exhibitors Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                @forelse($exhibitors->take(6) as $exhibitor)
                    <a href="{{ route('exhibitor.show', $exhibitor) }}"
                        class="group block h-full bg-zinc-50 rounded-xl p-6 hover:bg-zinc-100 hover:shadow-lg transition-all duration-300">
                        <!-- Header with Logo and Name -->
                        <div class="flex items-start gap-4 mb-4">
                            <div
                                class="w-14 h-14 bg-white rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm border border-zinc-100 group-hover:shadow-md transition-shadow">
                                @if ($exhibitor->logo_path)
                                    <img src="{{ Storage::url($exhibitor->logo_path) }}"
                                        alt="{{ $exhibitor->brand_name }}" class="w-10 h-10 object-contain rounded" />
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
                        <div class="h-full bg-zinc-50 rounded-xl p-6">
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
                    <a href="#register"
                        class="inline-flex items-center px-6 py-2.5 text-sm font-normal text-white bg-zinc-900 hover:bg-zinc-800 rounded-full transition-colors">
                        View All {{ $exhibitors->count() }} Exhibitors
                    </a>
                </div>
            @endif
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-zinc-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center mb-12">
                <span class="text-xs font-light tracking-widest text-zinc-400 uppercase">Why Visit</span>
                <h2 class="mt-3 text-2xl sm:text-3xl font-light text-zinc-900">
                    Find Your Dream Property
                </h2>
                <p class="mt-3 text-sm text-zinc-500 max-w-xl mx-auto font-light">
                    Discover exclusive properties and secure the best deals
                </p>
            </div>

            <!-- Features Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Feature 1 -->
                <div class="p-6 bg-white rounded-lg">
                    <div class="mb-4">
                        <div class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-zinc-100">
                            <x-heroicon-o-building-office class="w-5 h-5 text-zinc-600" />
                        </div>
                    </div>
                    <h3 class="text-sm font-normal text-zinc-900 mb-2">200+ Properties</h3>
                    <p class="text-xs text-zinc-500 font-light leading-relaxed">
                        Residential, commercial, and luxury properties from trusted developers.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="p-6 bg-white rounded-lg">
                    <div class="mb-4">
                        <div class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-zinc-100">
                            <x-heroicon-o-currency-rupee class="w-5 h-5 text-zinc-600" />
                        </div>
                    </div>
                    <h3 class="text-sm font-normal text-zinc-900 mb-2">Exclusive Offers</h3>
                    <p class="text-xs text-zinc-500 font-light leading-relaxed">
                        Special pricing and limited-time offers available only to exhibition visitors.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="p-6 bg-white rounded-lg">
                    <div class="mb-4">
                        <div class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-zinc-100">
                            <x-heroicon-o-user-group class="w-5 h-5 text-zinc-600" />
                        </div>
                    </div>
                    <h3 class="text-sm font-normal text-zinc-900 mb-2">Expert Consultations</h3>
                    <p class="text-xs text-zinc-500 font-light leading-relaxed">
                        Free advice from real estate experts, legal advisors, and financial consultants.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="p-6 bg-white rounded-lg">
                    <div class="mb-4">
                        <div class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-zinc-100">
                            <x-heroicon-o-users class="w-5 h-5 text-zinc-600" />
                        </div>
                    </div>
                    <h3 class="text-sm font-normal text-zinc-900 mb-2">Industry Network</h3>
                    <p class="text-xs text-zinc-500 font-light leading-relaxed">
                        Connect with developers, investors, and fellow property buyers.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="p-6 bg-white rounded-lg">
                    <div class="mb-4">
                        <div class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-zinc-100">
                            <x-heroicon-o-check-circle class="w-5 h-5 text-zinc-600" />
                        </div>
                    </div>
                    <h3 class="text-sm font-normal text-zinc-900 mb-2">Digital Experience</h3>
                    <p class="text-xs text-zinc-500 font-light leading-relaxed">
                        Digital registration, instant property brochures, and convenient online tools.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="p-6 bg-white rounded-lg">
                    <div class="mb-4">
                        <div class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-zinc-100">
                            <x-heroicon-o-chart-bar class="w-5 h-5 text-zinc-600" />
                        </div>
                    </div>
                    <h3 class="text-sm font-normal text-zinc-900 mb-2">Market Insights</h3>
                    <p class="text-xs text-zinc-500 font-light leading-relaxed">
                        Latest market research, price trends, and investment opportunities.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Sponsors Marquee Section -->
    <section class="py-12 bg-white border-y border-zinc-100 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
            <div class="text-center">
                <span class="text-xs font-light tracking-widest text-zinc-400 uppercase">Our Sponsors</span>
            </div>
        </div>

        <!-- Marquee Container -->
        <div class="relative">
            <!-- Gradient Overlays -->
            <div class="absolute left-0 top-0 bottom-0 w-32 bg-gradient-to-r from-white to-transparent z-10"></div>
            <div class="absolute right-0 top-0 bottom-0 w-32 bg-gradient-to-l from-white to-transparent z-10"></div>

            <!-- Marquee Track -->
            <div class="flex animate-marquee">
                <!-- First set of logos -->
                <div class="flex items-center gap-16 px-8">
                    <div class="flex-shrink-0 w-32 h-16 bg-zinc-50 rounded-lg flex items-center justify-center">
                        <span class="text-xs font-light text-zinc-400">Sponsor 1</span>
                    </div>
                    <div class="flex-shrink-0 w-32 h-16 bg-zinc-50 rounded-lg flex items-center justify-center">
                        <span class="text-xs font-light text-zinc-400">Sponsor 2</span>
                    </div>
                    <div class="flex-shrink-0 w-32 h-16 bg-zinc-50 rounded-lg flex items-center justify-center">
                        <span class="text-xs font-light text-zinc-400">Sponsor 3</span>
                    </div>
                    <div class="flex-shrink-0 w-32 h-16 bg-zinc-50 rounded-lg flex items-center justify-center">
                        <span class="text-xs font-light text-zinc-400">Sponsor 4</span>
                    </div>
                    <div class="flex-shrink-0 w-32 h-16 bg-zinc-50 rounded-lg flex items-center justify-center">
                        <span class="text-xs font-light text-zinc-400">Sponsor 5</span>
                    </div>
                    <div class="flex-shrink-0 w-32 h-16 bg-zinc-50 rounded-lg flex items-center justify-center">
                        <span class="text-xs font-light text-zinc-400">Sponsor 6</span>
                    </div>
                    <div class="flex-shrink-0 w-32 h-16 bg-zinc-50 rounded-lg flex items-center justify-center">
                        <span class="text-xs font-light text-zinc-400">Sponsor 7</span>
                    </div>
                    <div class="flex-shrink-0 w-32 h-16 bg-zinc-50 rounded-lg flex items-center justify-center">
                        <span class="text-xs font-light text-zinc-400">Sponsor 8</span>
                    </div>
                </div>
                <!-- Duplicate set for seamless loop -->
                <div class="flex items-center gap-16 px-8">
                    <div class="flex-shrink-0 w-32 h-16 bg-zinc-50 rounded-lg flex items-center justify-center">
                        <span class="text-xs font-light text-zinc-400">Sponsor 1</span>
                    </div>
                    <div class="flex-shrink-0 w-32 h-16 bg-zinc-50 rounded-lg flex items-center justify-center">
                        <span class="text-xs font-light text-zinc-400">Sponsor 2</span>
                    </div>
                    <div class="flex-shrink-0 w-32 h-16 bg-zinc-50 rounded-lg flex items-center justify-center">
                        <span class="text-xs font-light text-zinc-400">Sponsor 3</span>
                    </div>
                    <div class="flex-shrink-0 w-32 h-16 bg-zinc-50 rounded-lg flex items-center justify-center">
                        <span class="text-xs font-light text-zinc-400">Sponsor 4</span>
                    </div>
                    <div class="flex-shrink-0 w-32 h-16 bg-zinc-50 rounded-lg flex items-center justify-center">
                        <span class="text-xs font-light text-zinc-400">Sponsor 5</span>
                    </div>
                    <div class="flex-shrink-0 w-32 h-16 bg-zinc-50 rounded-lg flex items-center justify-center">
                        <span class="text-xs font-light text-zinc-400">Sponsor 6</span>
                    </div>
                    <div class="flex-shrink-0 w-32 h-16 bg-zinc-50 rounded-lg flex items-center justify-center">
                        <span class="text-xs font-light text-zinc-400">Sponsor 7</span>
                    </div>
                    <div class="flex-shrink-0 w-32 h-16 bg-zinc-50 rounded-lg flex items-center justify-center">
                        <span class="text-xs font-light text-zinc-400">Sponsor 8</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Registration CTA Section -->
    <section id="register" class="py-20 bg-zinc-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center mb-10">
                <span class="text-xs font-light tracking-widest text-zinc-500 uppercase">Free Registration</span>
                <h2 class="mt-3 text-2xl sm:text-3xl font-light text-white">
                    Get Your Free Pass
                </h2>
                <p class="mt-3 text-sm text-zinc-400 max-w-xl mx-auto font-light">
                    Register to receive your visitor pass and access all exhibitors
                </p>
            </div>

            <!-- Registration CTA -->
            <div class="max-w-lg mx-auto text-center">
                <div class="bg-zinc-800/50 border border-zinc-700/50 rounded-2xl p-8 backdrop-blur-sm">
                    <div class="mb-6">
                        <h3 class="text-xl font-light text-white mb-2">Quick & Easy Registration</h3>
                        <p class="text-sm text-zinc-400 font-light">
                            Complete your visitor registration in just 2 simple steps
                        </p>
                    </div>

                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <div class="text-center">
                            <div
                                class="w-8 h-8 bg-emerald-600/20 border border-emerald-600/30 rounded-full flex items-center justify-center mx-auto mb-2">
                                <span class="text-xs font-medium text-emerald-400">1</span>
                            </div>
                            <p class="text-xs text-zinc-400">Personal Info</p>
                        </div>
                        <div class="text-center">
                            <div
                                class="w-8 h-8 bg-emerald-600/20 border border-emerald-600/30 rounded-full flex items-center justify-center mx-auto mb-2">
                                <span class="text-xs font-medium text-emerald-400">2</span>
                            </div>
                            <p class="text-xs text-zinc-400">Preferences</p>
                        </div>
                        <div class="text-center">
                            <div
                                class="w-8 h-8 bg-emerald-600/20 border border-emerald-600/30 rounded-full flex items-center justify-center mx-auto mb-2">
                                <span class="text-xs font-medium text-emerald-400">✓</span>
                            </div>
                            <p class="text-xs text-zinc-400">Get Pass</p>
                        </div>
                    </div>

                    <a href="{{ route('visitor.register') }}"
                        class="inline-block w-full px-6 py-3 bg-white text-zinc-900 text-sm font-normal rounded-lg hover:bg-zinc-100 transition-colors">
                        Start Registration →
                    </a>
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
