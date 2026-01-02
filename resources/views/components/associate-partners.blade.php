@props(['partners'])

@if ($partners->count() > 0)
    <section class="py-12 bg-white border-b border-zinc-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center mb-10">
                <span class="text-xs font-semibold tracking-wider text-teal-600 uppercase">Our Partners</span>
                <h2 class="mt-2 text-2xl sm:text-3xl font-semibold text-zinc-900">
                    Associate Partners
                </h2>
                <div class="mt-3 w-16 h-1 bg-teal-600 mx-auto rounded-full"></div>
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
                                ? 'group block bg-gradient-to-br from-white to-zinc-50 rounded-2xl shadow-sm hover:shadow-2xl transition-all duration-500 overflow-hidden border border-zinc-200 hover:border-zinc-300 min-h-[160px]'
                                : 'group block bg-gradient-to-br from-white to-zinc-50 rounded-2xl shadow-sm overflow-hidden border border-zinc-200 cursor-default min-h-[160px]';
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
                                <div class="flex-1 p-4 bg-gradient-to-br from-white to-zinc-50 flex flex-col">
                                    <div class="flex items-start justify-between gap-3 mb-3">
                                        <h3
                                            class="text-base font-semibold text-zinc-900 leading-tight {{ $hasExhibitor ? 'group-hover:text-teal-700 transition-colors' : '' }}">
                                            {{ $company->company_name }}
                                        </h3>
                                        @if ($company->stall_number)
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 text-xs font-medium bg-zinc-100 text-zinc-700 rounded-md flex-shrink-0">
                                                {{ $company->stall_number }}
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Info Grid -->
                                    <div class="space-y-2 flex-grow">
                                        @if ($company->registered_number)
                                            <div class="flex items-center gap-2 text-sm text-zinc-600">
                                                <x-heroicon-o-phone class="w-4 h-4 text-zinc-400 flex-shrink-0" />
                                                <span>{{ $company->registered_number }}</span>
                                            </div>
                                        @endif

                                        @if ($hasExhibitor && $exhibitor->website)
                                            <div class="flex items-center gap-2 text-sm text-zinc-600">
                                                <x-heroicon-o-globe-alt class="w-4 h-4 text-zinc-400 flex-shrink-0" />
                                                <span class="truncate">{{ parse_url($exhibitor->website, PHP_URL_HOST) ?? $exhibitor->website }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- CTA -->
                                    @if ($hasExhibitor)
                                        <div
                                            class="flex items-center gap-1.5 mt-3 pt-3 border-t border-zinc-200 text-teal-600 group-hover:text-teal-700 transition-colors">
                                            <span class="text-sm font-medium">View Projects</span>
                                            <x-heroicon-o-arrow-right
                                                class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
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
                                ? 'group block bg-gradient-to-br from-white to-zinc-50 rounded-2xl shadow-sm hover:shadow-2xl transition-all duration-500 overflow-hidden border border-zinc-200 hover:border-zinc-300 min-h-[160px]'
                                : 'group block bg-gradient-to-br from-white to-zinc-50 rounded-2xl shadow-sm overflow-hidden border border-zinc-200 cursor-default min-h-[160px]';
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
                                <div class="flex-1 p-4 bg-gradient-to-br from-white to-zinc-50 flex flex-col">
                                    <div class="flex items-start justify-between gap-3 mb-3">
                                        <h3
                                            class="text-base font-semibold text-zinc-900 leading-tight {{ $hasExhibitor ? 'group-hover:text-teal-700 transition-colors' : '' }}">
                                            {{ $company->company_name }}
                                        </h3>
                                        @if ($company->stall_number)
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 text-xs font-medium bg-zinc-100 text-zinc-700 rounded-md flex-shrink-0">
                                                {{ $company->stall_number }}
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Info Grid -->
                                    <div class="space-y-2 flex-grow">
                                        @if ($company->registered_number)
                                            <div class="flex items-center gap-2 text-sm text-zinc-600">
                                                <x-heroicon-o-phone class="w-4 h-4 text-zinc-400 flex-shrink-0" />
                                                <span>{{ $company->registered_number }}</span>
                                            </div>
                                        @endif

                                        @if ($hasExhibitor && $exhibitor->website)
                                            <div class="flex items-center gap-2 text-sm text-zinc-600">
                                                <x-heroicon-o-globe-alt class="w-4 h-4 text-zinc-400 flex-shrink-0" />
                                                <span class="truncate">{{ parse_url($exhibitor->website, PHP_URL_HOST) ?? $exhibitor->website }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- CTA -->
                                    @if ($hasExhibitor)
                                        <div
                                            class="flex items-center gap-1.5 mt-3 pt-3 border-t border-zinc-200 text-teal-600 group-hover:text-teal-700 transition-colors">
                                            <span class="text-sm font-medium">View Projects</span>
                                            <x-heroicon-o-arrow-right
                                                class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
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
