@props(['partners'])

@if ($partners->count() > 0)
    <section class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center mb-8">
                <span class="text-xs font-light tracking-widest text-zinc-400 uppercase">Our Partners</span>
                <h2 class="mt-2 text-xl sm:text-2xl font-light text-zinc-900">
                    Co-Partners
                </h2>
            </div>

            <!-- Partners Grid: CS-1 to CS-8 (4 columns on desktop, 2 on tablet, 1 on mobile) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ($partners as $company)
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

                        <div class="flex items-stretch h-full">
                            <!-- Logo Section (Left) -->
                            <div class="w-24 flex-shrink-0 flex items-center justify-center p-3 bg-white border-r border-zinc-100">
                                @if ($hasExhibitor && ($exhibitor->preview_logo ?? $exhibitor->logo_path))
                                    <img src="{{ Storage::url($exhibitor->preview_logo ?? $exhibitor->logo_path) }}"
                                        alt="{{ $company->company_name }}"
                                        class="w-full h-full object-contain transition-transform duration-500 group-hover:scale-105" />
                                @else
                                    <x-heroicon-o-building-office class="w-10 h-10 text-zinc-300" />
                                @endif
                            </div>

                            <!-- Details Section (Right) -->
                            <div class="flex-1 p-2.5 bg-gradient-to-br from-zinc-50 to-white flex flex-col">
                                <div class="flex items-start justify-between mb-1.5">
                                    <h3
                                        class="text-xs font-medium text-zinc-900 {{ $hasExhibitor ? 'group-hover:text-teal-700 transition-colors' : '' }}">
                                        {{ $company->company_name }}
                                    </h3>
                                    @if ($company->stall_number)
                                        <span
                                            class="inline-flex items-center px-1.5 py-0.5 text-xs font-light bg-zinc-200 text-zinc-600 rounded flex-shrink-0 ml-1.5">
                                            {{ $company->stall_number }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Info Grid -->
                                <div class="space-y-0.5 flex-grow">
                                    @if ($hasExhibitor && $exhibitor->city)
                                        <div class="flex items-center gap-1 text-xs text-zinc-600">
                                            <x-heroicon-o-map-pin class="w-2.5 h-2.5 text-zinc-400 flex-shrink-0" />
                                            <span class="font-light truncate">{{ $exhibitor->city }}</span>
                                        </div>
                                    @endif

                                    @if ($company->registered_number)
                                        <div class="flex items-center gap-1 text-xs text-zinc-600">
                                            <x-heroicon-o-phone class="w-2.5 h-2.5 text-zinc-400 flex-shrink-0" />
                                            <span class="font-light truncate">{{ $company->registered_number }}</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- CTA -->
                                @if ($hasExhibitor)
                                    <div
                                        class="flex items-center gap-0.5 mt-1.5 pt-1.5 border-t border-zinc-200 text-teal-600 group-hover:text-teal-700 transition-colors">
                                        <span class="text-xs font-medium">View</span>
                                        <x-heroicon-o-arrow-right
                                            class="w-2.5 h-2.5 group-hover:translate-x-1 transition-transform" />
                                    </div>
                                @endif
                            </div>
                        </div>
                    </{{ $wrapperTag }}>
                @endforeach
            </div>
        </div>
    </section>
@endif
