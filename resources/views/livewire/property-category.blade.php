<div>
    <!-- Page Header -->
    <section class="bg-white border-b border-zinc-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-light text-zinc-900 mb-3">
                {{ $this->pageTitle }}
            </h1>
            <p class="text-sm text-zinc-600 font-light">
                Discover exhibitors offering {{ strtolower($this->pageTitle) }}
            </p>
        </div>
    </section>

    <!-- Companies Grid -->
    <section class="py-12 bg-zinc-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($exhibitors->count() > 0)
                <!-- Results Count -->
                <div class="mb-6">
                    <p class="text-sm font-light text-zinc-600">
                        @if ($exhibitors->count() === 1)
                            Showing 1 company
                        @else
                            Showing {{ $exhibitors->count() }} companies
                        @endif
                    </p>
                </div>

                <!-- Companies Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ($exhibitors as $exhibitor)
                        <a href="{{ route('exhibitor.show', $exhibitor) }}"
                            class="group block bg-gradient-to-br from-zinc-50 to-white rounded-2xl shadow-sm hover:shadow-2xl transition-all duration-500 overflow-hidden border border-zinc-200 hover:border-zinc-300">
                            <div class="flex items-stretch h-full">
                                <!-- Logo Section (Left) -->
                                <div
                                    class="w-24 flex-shrink-0 flex items-center justify-center p-3 bg-white border-r border-zinc-100">
                                    @if ($exhibitor->preview_logo ?? $exhibitor->logo_path)
                                        <img src="{{ Storage::url($exhibitor->preview_logo ?? $exhibitor->logo_path) }}"
                                            alt="{{ $exhibitor->brand_name }}"
                                            class="w-full h-full object-contain transition-transform duration-500 group-hover:scale-105" />
                                    @else
                                        <x-heroicon-o-building-office class="w-10 h-10 text-zinc-300" />
                                    @endif
                                </div>

                                <!-- Details Section (Right) -->
                                <div class="flex-1 p-3 bg-gradient-to-br from-zinc-50 to-white flex flex-col">
                                    <div class="flex items-start justify-between gap-2 mb-2">
                                        <h3
                                            class="text-sm font-semibold text-zinc-900 leading-tight group-hover:text-teal-700 transition-colors">
                                            {{ $exhibitor->brand_name }}
                                        </h3>
                                        @if ($exhibitor->company?->stall_number)
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-zinc-100 text-zinc-700 rounded flex-shrink-0">
                                                {{ $exhibitor->company->stall_number }}
                                            </span>
                                        @endif
                                    </div>

                                    <!-- CTA -->
                                    <div class="flex items-center gap-1 mt-auto pt-2 text-teal-600 group-hover:text-teal-700 transition-colors">
                                        <span class="text-xs font-medium">View Details</span>
                                        <x-heroicon-o-arrow-right
                                            class="w-3 h-3 group-hover:translate-x-1 transition-transform" />
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-xl p-12 text-center">
                    <div class="w-16 h-16 bg-zinc-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <x-heroicon-o-building-office class="w-8 h-8 text-zinc-400" />
                    </div>
                    <h3 class="text-lg font-normal text-zinc-900 mb-2">No companies found</h3>
                    <p class="text-sm font-light text-zinc-500 mb-6 max-w-md mx-auto">
                        We couldn't find any companies offering {{ strtolower($this->pageTitle) }} at this time.
                    </p>
                    <flux:button href="{{ route('public.exhibitors') }}" variant="primary">
                        View All Exhibitors
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
            <flux:button href="{{ route('visitor.register') }}" variant="primary">
                Get Your Free Pass
            </flux:button>
        </div>
    </section>
</div>
