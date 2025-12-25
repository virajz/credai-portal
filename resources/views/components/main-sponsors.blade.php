@props(['sponsors'])

@if ($sponsors->count() > 0)
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center mb-16">
                <span class="text-xs font-light tracking-widest text-zinc-400 uppercase">Powered By</span>
                <h2 class="mt-3 text-3xl sm:text-4xl font-light text-zinc-900">
                    Our Main Partners
                </h2>
                <p class="mt-3 text-sm text-zinc-500 max-w-2xl mx-auto font-light">
                    Meet the leading partners making this event possible
                </p>
            </div>

            <!-- Sponsors Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($sponsors as $company)
                    @php
                        $exhibitor = $company->exhibitor;
                        $hasExhibitor = $exhibitor !== null;
                        $wrapperTag = $hasExhibitor ? 'a' : 'div';
                        $wrapperClasses = $hasExhibitor
                            ? 'group block bg-gradient-to-br from-zinc-50 to-white rounded-2xl shadow-sm hover:shadow-2xl transition-all duration-500 overflow-hidden border border-zinc-200 hover:border-zinc-300'
                            : 'group block bg-gradient-to-br from-zinc-50 to-white rounded-2xl shadow-sm overflow-hidden border border-zinc-200 cursor-default';
                    @endphp

                    <{{ $wrapperTag }}
                        @if ($hasExhibitor) href="{{ route('exhibitor.show', $exhibitor) }}" @endif
                        class="{{ $wrapperClasses }}">

                        <!-- Logo Section -->
                        <div class="aspect-[4/3] flex items-center justify-center p-12 bg-white">
                            @if ($hasExhibitor && ($exhibitor->preview_logo ?? $exhibitor->logo_path))
                                <img src="{{ Storage::url($exhibitor->preview_logo ?? $exhibitor->logo_path) }}"
                                    alt="{{ $company->company_name }}"
                                    class="w-full h-full object-contain transition-transform duration-500 group-hover:scale-105" />
                            @else
                                <div class="text-center">
                                    <x-heroicon-o-building-office class="w-24 h-24 text-zinc-300 mx-auto mb-3" />
                                    <p class="text-sm font-light text-zinc-400">{{ $company->company_name }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- Details Section -->
                        <div class="p-6 bg-gradient-to-br from-zinc-50 to-white border-t border-zinc-100">
                            <h3
                                class="text-xl font-normal text-zinc-900 mb-3 {{ $hasExhibitor ? 'group-hover:text-teal-700 transition-colors' : '' }}">
                                {{ $company->company_name }}
                            </h3>

                            <!-- Info Grid -->
                            <div class="space-y-2.5">
                                @if ($hasExhibitor && $exhibitor->city)
                                    <div class="flex items-center gap-2.5 text-sm text-zinc-600">
                                        <x-heroicon-o-map-pin class="w-4 h-4 text-zinc-400 flex-shrink-0" />
                                        <span class="font-light">{{ $exhibitor->city }}</span>
                                    </div>
                                @endif

                                @if ($company->registered_number)
                                    <div class="flex items-center gap-2.5 text-sm text-zinc-600">
                                        <x-heroicon-o-phone class="w-4 h-4 text-zinc-400 flex-shrink-0" />
                                        <span class="font-light">{{ $company->registered_number }}</span>
                                    </div>
                                @endif

                                @if ($hasExhibitor && $exhibitor->website)
                                    <div class="flex items-center gap-2.5 text-sm text-zinc-600">
                                        <x-heroicon-o-globe-alt class="w-4 h-4 text-zinc-400 flex-shrink-0" />
                                        <span
                                            class="font-light truncate">{{ parse_url($exhibitor->website, PHP_URL_HOST) ?? $exhibitor->website }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- CTA -->
                            @if ($hasExhibitor)
                                <div
                                    class="flex items-center gap-2 mt-5 pt-5 border-t border-zinc-200 text-teal-600 group-hover:text-teal-700 transition-colors">
                                    <span class="text-sm font-medium">View Projects</span>
                                    <x-heroicon-o-arrow-right
                                        class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                                </div>
                            @else
                                <div class="flex items-center gap-2 mt-5 pt-5 border-t border-zinc-200 text-zinc-400">
                                    <span class="text-xs font-light">Details coming soon</span>
                                </div>
                            @endif
                        </div>
                        </{{ $wrapperTag }}>
                @endforeach
            </div>
        </div>
    </section>
@endif
