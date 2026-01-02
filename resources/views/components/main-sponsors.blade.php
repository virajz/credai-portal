@props(['sponsors'])

@if ($sponsors->count() > 0)
    <section class="py-16 bg-white border-b border-zinc-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center mb-12">
                <span class="text-xs font-semibold tracking-wider text-teal-600 uppercase">Powered By</span>
                <h2 class="mt-2 text-3xl sm:text-4xl font-semibold text-zinc-900">
                    Our Main Partners
                </h2>
                <div class="mt-3 w-16 h-1 bg-teal-600 mx-auto rounded-full"></div>
            </div>

            <!-- Sponsors Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($sponsors as $company)
                    @php
                        $exhibitor = $company->exhibitor;
                        $hasExhibitor = $exhibitor !== null;
                        $wrapperTag = $hasExhibitor ? 'a' : 'div';
                        $wrapperClasses = $hasExhibitor
                            ? 'group block bg-white rounded-xl shadow-md hover:shadow-2xl transition-all duration-300 overflow-hidden border-2 border-zinc-100 hover:border-teal-500'
                            : 'group block bg-white rounded-xl shadow-md overflow-hidden border-2 border-zinc-100 cursor-default';
                    @endphp

                    <{{ $wrapperTag }}
                        @if ($hasExhibitor) href="{{ route('exhibitor.show', $exhibitor) }}" @endif
                        class="{{ $wrapperClasses }}">

                        <!-- Logo Section -->
                        <div class="aspect-[16/9] flex items-center justify-center p-8 bg-gradient-to-br from-white to-zinc-50 border-b-2 border-zinc-100">
                            @if ($hasExhibitor && ($exhibitor->preview_logo ?? $exhibitor->logo_path))
                                <img src="{{ Storage::url($exhibitor->preview_logo ?? $exhibitor->logo_path) }}"
                                    alt="{{ $company->company_name }}"
                                    class="w-full h-full object-contain transition-transform duration-300 group-hover:scale-110" />
                            @else
                                <div class="text-center">
                                    <x-heroicon-o-building-office class="w-20 h-20 text-zinc-300 mx-auto mb-2" />
                                    <p class="text-sm font-medium text-zinc-400">{{ $company->company_name }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- Details Section -->
                        <div class="p-5 bg-white">
                            <h3
                                class="text-lg font-semibold text-zinc-900 mb-3 {{ $hasExhibitor ? 'group-hover:text-teal-600 transition-colors' : '' }}">
                                {{ $company->company_name }}
                            </h3>

                            <!-- Info Grid -->
                            <div class="space-y-2">
                                @if ($company->registered_number)
                                    <div class="flex items-center gap-2 text-sm text-zinc-600">
                                        <x-heroicon-o-phone class="w-4 h-4 text-teal-500 flex-shrink-0" />
                                        <span>{{ $company->registered_number }}</span>
                                    </div>
                                @endif

                                @if ($hasExhibitor && $exhibitor->website)
                                    <div class="flex items-center gap-2 text-sm text-zinc-600">
                                        <x-heroicon-o-globe-alt class="w-4 h-4 text-teal-500 flex-shrink-0" />
                                        <span class="truncate">{{ parse_url($exhibitor->website, PHP_URL_HOST) ?? $exhibitor->website }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- CTA -->
                            @if ($hasExhibitor)
                                <div
                                    class="flex items-center justify-between mt-4 pt-4 border-t border-zinc-100">
                                    <span class="text-sm font-semibold text-teal-600 group-hover:text-teal-700">Explore Projects</span>
                                    <x-heroicon-o-arrow-right
                                        class="w-5 h-5 text-teal-600 group-hover:translate-x-1 transition-transform" />
                                </div>
                            @else
                                <div class="mt-4 pt-4 border-t border-zinc-100">
                                    <span class="text-xs text-zinc-400">Details coming soon</span>
                                </div>
                            @endif
                        </div>
                    </{{ $wrapperTag }}>
                @endforeach
            </div>
        </div>
    </section>
@endif
