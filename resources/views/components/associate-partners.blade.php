@props(['partners'])

@if ($partners->count() > 0)
    <section class="py-12 bg-zinc-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center mb-8">
                <span class="text-xs font-light tracking-widest text-zinc-400 uppercase">Our Partners</span>
                <h2 class="mt-2 text-xl sm:text-2xl font-light text-zinc-900">
                    Associate Partners
                </h2>
            </div>

            <!-- First Row: A-1, A-2, A-3 (3 columns) -->
            @php
                $firstRow = $partners->filter(fn($p) => in_array($p->stall_number, ['A-1', 'A-2', 'A-3']));
                $secondRow = $partners->filter(fn($p) => in_array($p->stall_number, ['A-4', 'A-5']));
            @endphp

            @if ($firstRow->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                    @foreach ($firstRow as $company)
                        @php
                            $exhibitor = $company->exhibitor;
                            $hasExhibitor = $exhibitor !== null;
                            $wrapperTag = $hasExhibitor ? 'a' : 'div';
                            $wrapperClasses = $hasExhibitor
                                ? 'group block bg-gradient-to-br from-white to-zinc-50 rounded-2xl shadow-sm hover:shadow-2xl transition-all duration-500 overflow-hidden border border-zinc-200 hover:border-zinc-300 min-h-[140px]'
                                : 'group block bg-gradient-to-br from-white to-zinc-50 rounded-2xl shadow-sm overflow-hidden border border-zinc-200 cursor-default min-h-[140px]';
                        @endphp

                        <{{ $wrapperTag }}
                            @if ($hasExhibitor) href="{{ route('exhibitor.show', $exhibitor) }}" @endif
                            class="{{ $wrapperClasses }}">

                            <div class="flex items-stretch h-full">
                                <!-- Logo Section (Left) -->
                                <div class="w-40 flex-shrink-0 flex items-center justify-center p-4 bg-white border-r border-zinc-100">
                                    @if ($hasExhibitor && ($exhibitor->preview_logo ?? $exhibitor->logo_path))
                                        <img src="{{ Storage::url($exhibitor->preview_logo ?? $exhibitor->logo_path) }}"
                                            alt="{{ $company->company_name }}"
                                            class="w-full h-full object-contain transition-transform duration-500 group-hover:scale-105" />
                                    @else
                                        <x-heroicon-o-building-office class="w-16 h-16 text-zinc-300" />
                                    @endif
                                </div>

                                <!-- Details Section (Right) -->
                                <div class="flex-1 p-3 bg-gradient-to-br from-white to-zinc-50 flex flex-col">
                                    <div class="flex items-start justify-between mb-2">
                                        <h3
                                            class="text-sm font-medium text-zinc-900 {{ $hasExhibitor ? 'group-hover:text-teal-700 transition-colors' : '' }}">
                                            {{ $company->company_name }}
                                        </h3>
                                        @if ($company->stall_number)
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 text-xs font-light bg-zinc-200 text-zinc-600 rounded flex-shrink-0 ml-2">
                                                {{ $company->stall_number }}
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Info Grid -->
                                    <div class="space-y-1 flex-grow">
                                        @if ($hasExhibitor && $exhibitor->city)
                                            <div class="flex items-center gap-1.5 text-xs text-zinc-600">
                                                <x-heroicon-o-map-pin class="w-3 h-3 text-zinc-400 flex-shrink-0" />
                                                <span class="font-light">{{ $exhibitor->city }}</span>
                                            </div>
                                        @endif

                                        @if ($company->registered_number)
                                            <div class="flex items-center gap-1.5 text-xs text-zinc-600">
                                                <x-heroicon-o-phone class="w-3 h-3 text-zinc-400 flex-shrink-0" />
                                                <span class="font-light">{{ $company->registered_number }}</span>
                                            </div>
                                        @endif

                                        @if ($hasExhibitor && $exhibitor->website)
                                            <div class="flex items-center gap-1.5 text-xs text-zinc-600">
                                                <x-heroicon-o-globe-alt class="w-3 h-3 text-zinc-400 flex-shrink-0" />
                                                <span
                                                    class="font-light truncate">{{ parse_url($exhibitor->website, PHP_URL_HOST) ?? $exhibitor->website }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- CTA -->
                                    @if ($hasExhibitor)
                                        <div
                                            class="flex items-center gap-1 mt-2 pt-2 border-t border-zinc-200 text-teal-600 group-hover:text-teal-700 transition-colors">
                                            <span class="text-xs font-medium">View Projects</span>
                                            <x-heroicon-o-arrow-right
                                                class="w-3 h-3 group-hover:translate-x-1 transition-transform" />
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </{{ $wrapperTag }}>
                    @endforeach
                </div>
            @endif

            <!-- Second Row: A-4, A-5 (2 columns centered) -->
            @if ($secondRow->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-4xl mx-auto">
                    @foreach ($secondRow as $company)
                        @php
                            $exhibitor = $company->exhibitor;
                            $hasExhibitor = $exhibitor !== null;
                            $wrapperTag = $hasExhibitor ? 'a' : 'div';
                            $wrapperClasses = $hasExhibitor
                                ? 'group block bg-gradient-to-br from-white to-zinc-50 rounded-2xl shadow-sm hover:shadow-2xl transition-all duration-500 overflow-hidden border border-zinc-200 hover:border-zinc-300 min-h-[140px]'
                                : 'group block bg-gradient-to-br from-white to-zinc-50 rounded-2xl shadow-sm overflow-hidden border border-zinc-200 cursor-default min-h-[140px]';
                        @endphp

                        <{{ $wrapperTag }}
                            @if ($hasExhibitor) href="{{ route('exhibitor.show', $exhibitor) }}" @endif
                            class="{{ $wrapperClasses }}">

                            <div class="flex items-stretch h-full">
                                <!-- Logo Section (Left) -->
                                <div class="w-40 flex-shrink-0 flex items-center justify-center p-4 bg-white border-r border-zinc-100">
                                    @if ($hasExhibitor && ($exhibitor->preview_logo ?? $exhibitor->logo_path))
                                        <img src="{{ Storage::url($exhibitor->preview_logo ?? $exhibitor->logo_path) }}"
                                            alt="{{ $company->company_name }}"
                                            class="w-full h-full object-contain transition-transform duration-500 group-hover:scale-105" />
                                    @else
                                        <x-heroicon-o-building-office class="w-16 h-16 text-zinc-300" />
                                    @endif
                                </div>

                                <!-- Details Section (Right) -->
                                <div class="flex-1 p-3 bg-gradient-to-br from-white to-zinc-50 flex flex-col">
                                    <div class="flex items-start justify-between mb-2">
                                        <h3
                                            class="text-sm font-medium text-zinc-900 {{ $hasExhibitor ? 'group-hover:text-teal-700 transition-colors' : '' }}">
                                            {{ $company->company_name }}
                                        </h3>
                                        @if ($company->stall_number)
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 text-xs font-light bg-zinc-200 text-zinc-600 rounded flex-shrink-0 ml-2">
                                                {{ $company->stall_number }}
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Info Grid -->
                                    <div class="space-y-1 flex-grow">
                                        @if ($hasExhibitor && $exhibitor->city)
                                            <div class="flex items-center gap-1.5 text-xs text-zinc-600">
                                                <x-heroicon-o-map-pin class="w-3 h-3 text-zinc-400 flex-shrink-0" />
                                                <span class="font-light">{{ $exhibitor->city }}</span>
                                            </div>
                                        @endif

                                        @if ($company->registered_number)
                                            <div class="flex items-center gap-1.5 text-xs text-zinc-600">
                                                <x-heroicon-o-phone class="w-3 h-3 text-zinc-400 flex-shrink-0" />
                                                <span class="font-light">{{ $company->registered_number }}</span>
                                            </div>
                                        @endif

                                        @if ($hasExhibitor && $exhibitor->website)
                                            <div class="flex items-center gap-1.5 text-xs text-zinc-600">
                                                <x-heroicon-o-globe-alt class="w-3 h-3 text-zinc-400 flex-shrink-0" />
                                                <span
                                                    class="font-light truncate">{{ parse_url($exhibitor->website, PHP_URL_HOST) ?? $exhibitor->website }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- CTA -->
                                    @if ($hasExhibitor)
                                        <div
                                            class="flex items-center gap-1 mt-2 pt-2 border-t border-zinc-200 text-teal-600 group-hover:text-teal-700 transition-colors">
                                            <span class="text-xs font-medium">View Projects</span>
                                            <x-heroicon-o-arrow-right
                                                class="w-3 h-3 group-hover:translate-x-1 transition-transform" />
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </{{ $wrapperTag }}>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endif
