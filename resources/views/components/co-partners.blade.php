@props(['partners'])

@if ($partners->count() > 0)
    <section class="py-12 bg-white border-b border-zinc-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center mb-10">
                <span class="text-xs font-semibold tracking-wider text-teal-600 uppercase">Our Partners</span>
                <h2 class="mt-2 text-2xl sm:text-3xl font-semibold text-zinc-900">
                    Co-Partners
                </h2>
                <div class="mt-3 w-16 h-1 bg-teal-600 mx-auto rounded-full"></div>
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
                            <div class="flex-1 p-3 bg-gradient-to-br from-zinc-50 to-white flex flex-col">
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <h3
                                        class="text-sm font-semibold text-zinc-900 leading-tight {{ $hasExhibitor ? 'group-hover:text-teal-700 transition-colors' : '' }}">
                                        {{ $company->company_name }}
                                    </h3>
                                    @if ($company->stall_number)
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-zinc-100 text-zinc-700 rounded flex-shrink-0">
                                            {{ $company->stall_number }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Info Grid -->
                                <div class="space-y-1.5 flex-grow">
                                    @if ($company->registered_number)
                                        <div class="flex items-center gap-1.5 text-xs text-zinc-600">
                                            <x-heroicon-o-phone class="w-3.5 h-3.5 text-zinc-400 flex-shrink-0" />
                                            <span class="truncate">{{ $company->registered_number }}</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- CTA -->
                                @if ($hasExhibitor)
                                    <div
                                        class="flex items-center gap-1 mt-2 pt-2 border-t border-zinc-200 text-teal-600 group-hover:text-teal-700 transition-colors">
                                        <span class="text-xs font-medium">View</span>
                                        <x-heroicon-o-arrow-right
                                            class="w-3 h-3 group-hover:translate-x-1 transition-transform" />
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
