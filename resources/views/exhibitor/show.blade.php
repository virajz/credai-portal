<x-layouts.front :title="$exhibitor->brand_name . ' - ' . config('app.name')" :back-link="route('public.exhibitors')" back-text="Back to Exhibitors" body-class="bg-zinc-50">

    <!-- Hero Section -->
    <section class="bg-white border-b border-zinc-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <!-- Breadcrumb -->
            <nav class="mb-4">
                <ol class="flex items-center gap-2 text-xs font-light">
                    <li>
                        <a href="{{ route('home') }}" class="text-zinc-500 hover:text-zinc-900 transition-colors">Home</a>
                    </li>
                    <li class="text-zinc-400">/</li>
                    <li>
                        <a href="{{ route('public.exhibitors') }}"
                            class="text-zinc-500 hover:text-zinc-900 transition-colors">Exhibitors</a>
                    </li>
                    <li class="text-zinc-400">/</li>
                    <li class="text-zinc-900">{{ $exhibitor->brand_name }}</li>
                </ol>
            </nav>

            <div class="flex items-center justify-between gap-6">
                <div class="flex items-center gap-4 sm:gap-6 flex-1 min-w-0">
                    <!-- Logo Box -->
                    <div class="w-16 h-16 sm:w-20 sm:h-20 flex items-center justify-center flex-shrink-0">
                        @if ($exhibitor->preview_logo ?? $exhibitor->logo_path)
                            <img src="{{ Storage::url($exhibitor->preview_logo ?? $exhibitor->logo_path) }}"
                                alt="{{ $exhibitor->brand_name }}" class="w-full h-full object-contain" />
                        @else
                            <x-heroicon-o-building-office class="w-8 h-8 text-zinc-400" />
                        @endif
                    </div>

                    <!-- Info -->
                    <div class="flex-1 min-w-0">
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-light text-zinc-900">
                            {{ $exhibitor->brand_name }}
                        </h1>
                        @if ($exhibitor->city)
                            <p class="flex items-center gap-1.5 text-sm font-light text-zinc-600 mt-1">
                                <x-heroicon-o-map-pin class="w-3.5 h-3.5" />
                                {{ $exhibitor->city }}
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Stall badge on the right -->
                @if ($exhibitor->company?->stall_number)
                    <div class="flex-shrink-0">
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 text-[10px] font-normal bg-zinc-900 text-white rounded-full">
                            Stall {{ $exhibitor->company->stall_number }}
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main class="py-10 lg:py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column - Main Content -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- About Section -->
                    @if ($exhibitor->additional_details || $exhibitor->facia_name)
                        <div class="bg-white rounded-xl p-6 shadow-sm">
                            <h2 class="text-sm font-medium text-zinc-900 mb-4">About</h2>
                            @if ($exhibitor->facia_name)
                                <p class="text-xs font-light text-zinc-500 mb-3">{{ $exhibitor->facia_name }}</p>
                            @endif
                            @if ($exhibitor->additional_details)
                                <p class="text-sm text-zinc-600 font-light leading-relaxed">
                                    {{ $exhibitor->additional_details }}
                                </p>
                            @endif
                        </div>
                    @endif

                    <!-- Projects Section -->
                    @if ($exhibitor->projects->count() > 0)
                        @php
                            $projectsByCategory = $exhibitor->projects->groupBy('category')->sortKeys();
                        @endphp

                        <div class="space-y-6">
                            @foreach ($projectsByCategory as $category => $categoryProjects)
                                <div class="bg-white rounded-xl p-6 shadow-sm">
                                    <!-- Category Header -->
                                    <div class="mb-5">
                                        <div class="flex items-center justify-between">
                                            <h3 class="text-sm font-medium text-zinc-900">
                                                {{ $category ?: 'Uncategorized' }}
                                            </h3>
                                            <span class="text-xs font-light text-zinc-400">
                                                {{ $categoryProjects->count() }}
                                                {{ Str::plural('project', $categoryProjects->count()) }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Projects Grid -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                        @foreach ($categoryProjects as $project)
                                            <a href="{{ route('project.show', $project) }}"
                                                class="group block bg-zinc-50 hover:bg-zinc-100 rounded-lg p-3 transition-colors">
                                                <div class="flex flex-col h-full">
                                                    <!-- Logo/Icon -->
                                                    <div
                                                        class="w-full aspect-square bg-white rounded-lg flex items-center justify-center mb-3 overflow-hidden">
                                                        @if ($project->logo_path)
                                                            <img src="{{ Storage::url($project->logo_path) }}"
                                                                alt="{{ $project->name }}"
                                                                class="w-full h-full object-contain p-2" />
                                                        @else
                                                            @php
                                                                $categoryLower = strtolower($category ?? '');
                                                                $icon = 'building-office';

                                                                if (
                                                                    str_contains($categoryLower, 'villa') ||
                                                                    str_contains($categoryLower, 'bungalow') ||
                                                                    str_contains($categoryLower, 'weekend')
                                                                ) {
                                                                    $icon = 'home-modern';
                                                                } elseif (
                                                                    str_contains($categoryLower, 'bhk') ||
                                                                    str_contains($categoryLower, 'apartment') ||
                                                                    str_contains($categoryLower, 'flat')
                                                                ) {
                                                                    $icon = 'building-office-2';
                                                                } elseif (
                                                                    str_contains($categoryLower, 'plot') ||
                                                                    str_contains($categoryLower, 'land')
                                                                ) {
                                                                    $icon = 'square-3-stack-3d';
                                                                } elseif (
                                                                    str_contains($categoryLower, 'commercial') ||
                                                                    str_contains($categoryLower, 'office') ||
                                                                    str_contains($categoryLower, 'showroom') ||
                                                                    str_contains($categoryLower, 'shop')
                                                                ) {
                                                                    $icon = 'building-storefront';
                                                                } elseif (str_contains($categoryLower, 'penthouse')) {
                                                                    $icon = 'building-office';
                                                                }
                                                            @endphp
                                                            <x-dynamic-component :component="'heroicon-o-' . $icon"
                                                                class="w-12 h-12 text-zinc-300" />
                                                        @endif
                                                    </div>

                                                    <!-- Header -->
                                                    <div class="flex-1 mb-2">
                                                        <h4
                                                            class="text-xs font-medium text-zinc-900 group-hover:text-zinc-700 line-clamp-2 mb-1.5">
                                                            {{ $project->name }}
                                                        </h4>
                                                        @if ($project->status)
                                                            <span
                                                                class="inline-flex items-center px-1.5 py-0.5 text-[10px] font-normal rounded-full
                                                                {{ $project->status === 'completed' ? 'bg-teal-100 text-teal-700' : '' }}
                                                                {{ $project->status === 'ongoing' ? 'bg-blue-100 text-blue-700' : '' }}
                                                                {{ $project->status === 'upcoming' ? 'bg-amber-100 text-amber-700' : '' }}
                                                                {{ !in_array($project->status, ['completed', 'ongoing', 'upcoming']) ? 'bg-zinc-200 text-zinc-600' : '' }}">
                                                                {{ ucfirst($project->status) }}
                                                            </span>
                                                        @endif
                                                    </div>

                                                    <!-- Details -->
                                                    <div class="space-y-1 mb-2">
                                                        @if ($project->area)
                                                            <div
                                                                class="flex items-center gap-1 text-[11px] text-zinc-600">
                                                                <x-heroicon-o-map-pin
                                                                    class="w-3 h-3 text-zinc-400 flex-shrink-0" />
                                                                <span
                                                                    class="font-light truncate">{{ $project->area }}</span>
                                                            </div>
                                                        @endif
                                                        @if ($project->handover_date)
                                                            <div
                                                                class="flex items-center gap-1 text-[11px] text-zinc-600">
                                                                <x-heroicon-o-calendar
                                                                    class="w-3 h-3 text-zinc-400 flex-shrink-0" />
                                                                <span
                                                                    class="font-light truncate">{{ $project->handover_date }}</span>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <!-- Footer -->
                                                    <div
                                                        class="flex items-center justify-between gap-1 pt-2 border-t border-zinc-200">
                                                        @if ($project->pdf_path)
                                                            <flux:button
                                                                onclick="event.preventDefault(); event.stopPropagation(); window.open('{{ route('track.project.brochure', $project) }}', '_blank');"
                                                                variant="ghost" size="xs" icon="arrow-down-tray">
                                                                Brochure
                                                            </flux:button>
                                                        @else
                                                            <span></span>
                                                        @endif
                                                        <span
                                                            class="inline-flex items-center justify-center w-6 h-6 text-zinc-400 group-hover:text-zinc-600 transition-colors">
                                                            <x-heroicon-o-arrow-right class="w-3.5 h-3.5" />
                                                        </span>
                                                    </div>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="bg-white rounded-xl p-6 shadow-sm">
                            <div class="text-center py-8">
                                <div
                                    class="w-12 h-12 bg-zinc-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                                    <x-heroicon-o-building-office class="w-6 h-6 text-zinc-400" />
                                </div>
                                <p class="text-sm font-light text-zinc-500 mb-1">No projects listed yet</p>
                                <p class="text-xs font-light text-zinc-400">Check back soon for updates</p>
                            </div>
                        </div>
                    @endif
                    @endif

                    <!-- Company Video -->
                    @if ($exhibitor->video_url)
                        <div class="bg-white rounded-xl p-6 shadow-sm">
                            <h2 class="text-sm font-medium text-zinc-900 mb-4">Company Video</h2>
                            <div class="aspect-video rounded-lg overflow-hidden bg-zinc-100">
                                @if (str_contains($exhibitor->video_url, 'youtube') || str_contains($exhibitor->video_url, 'youtu.be'))
                                    @php
                                        preg_match(
                                            '/(?:youtube\.com\/(?:[^\/]+\/.+\/(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/',
                                            $exhibitor->video_url,
                                            $matches,
                                        );
                                        $videoId = $matches[1] ?? '';
                                    @endphp
                                    @if ($videoId)
                                        <iframe src="https://www.youtube.com/embed/{{ $videoId }}"
                                            class="w-full h-full" frameborder="0"
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                            allowfullscreen>
                                        </iframe>
                                    @endif
                                @else
                                    <a href="{{ $exhibitor->video_url }}" target="_blank" rel="noopener"
                                        class="flex items-center justify-center h-full text-zinc-500 hover:text-zinc-700 transition-colors">
                                        <div class="text-center">
                                            <x-heroicon-o-play-circle class="w-12 h-12 mx-auto mb-2" />
                                            <span class="text-sm font-light">Watch Video</span>
                                        </div>
                                    </a>
                                @endif
                            </div>
                        </div>

                    <!-- Social Media & Connect -->
                    @if ($exhibitor->social_media_links && count($exhibitor->social_media_links) > 0)
                        @php
                            $socialIcons = [
                                'facebook' => 'bi-facebook',
                                'instagram' => 'bi-instagram',
                                'linkedin' => 'bi-linkedin',
                                'youtube' => 'bi-youtube',
                                'whatsapp' => 'bi-whatsapp',
                            ];
                        @endphp

                        <div class="bg-white rounded-xl p-6 shadow-sm">
                            <h2 class="text-sm font-medium text-zinc-900 mb-4">Connect</h2>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($exhibitor->social_media_links as $platform => $url)
                                    @if ($url)
                                        @php $icon = $socialIcons[$platform] ?? null; @endphp
                                        <flux:button href="{{ $url }}" target="_blank" rel="noopener"
                                            variant="subtle" size="sm" class="text-xs font-normal">
                                            @if ($icon)
                                                <x-slot name="iconLeading">
                                                    @if ($icon === 'bi-facebook')
                                                        <x-bi-facebook />
                                                    @elseif ($icon === 'bi-instagram')
                                                        <x-bi-instagram />
                                                    @elseif ($icon === 'bi-linkedin')
                                                        <x-bi-linkedin />
                                                    @elseif ($icon === 'bi-youtube')
                                                        <x-bi-youtube />
                                                    @elseif ($icon === 'bi-whatsapp')
                                                        <x-bi-whatsapp />
                                                    @else
                                                        {{-- fallback to globe icon component --}}
                                                        <x-heroicon-o-globe-alt class="w-4 h-4" />
                                                    @endif
                                                </x-slot>
                                            @endif
                                            {{ ucfirst($platform) }}
                                        </flux:button>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right Column - Contact Sidebar -->
                <div class="lg:col-span-1">
                    <div class="lg:sticky lg:top-28 space-y-6">
                        <!-- Contact Card -->
                        <div class="bg-white rounded-xl p-6 shadow-sm">
                            <h2 class="text-sm font-medium text-zinc-900 mb-5">Contact Information</h2>

                            <div class="space-y-4">
                                @if ($exhibitor->office_address)
                                    <div class="flex items-start gap-3">
                                        <div
                                            class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <x-heroicon-o-map-pin class="w-4 h-4 text-blue-600" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <span
                                                class="text-[10px] font-light text-zinc-400 uppercase tracking-wide">Address</span>
                                            <p class="text-sm text-zinc-700 font-light mt-0.5">
                                                {{ $exhibitor->office_address }}</p>
                                        </div>
                                    </div>
                                @endif

                                @if ($exhibitor->phone_number)
                                    <div class="flex items-start gap-3">
                                        <div
                                            class="w-8 h-8 bg-teal-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <x-heroicon-o-phone class="w-4 h-4 text-teal-600" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <span
                                                class="text-[10px] font-light text-zinc-400 uppercase tracking-wide">Phone</span>
                                            <p class="text-sm text-zinc-700 font-light mt-0.5">
                                                {{ $exhibitor->phone_number }}</p>
                                        </div>
                                    </div>
                                @endif

                                @if ($exhibitor->email)
                                    <div class="flex items-start gap-3">
                                        <div
                                            class="w-8 h-8 bg-purple-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <x-heroicon-o-envelope class="w-4 h-4 text-purple-600" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <span
                                                class="text-[10px] font-light text-zinc-400 uppercase tracking-wide">Email</span>
                                            <p class="text-sm text-zinc-700 font-light mt-0.5 break-all">
                                                {{ $exhibitor->email }}</p>
                                        </div>
                                    </div>
                                @endif

                                @if ($exhibitor->website)
                                    <div class="flex items-start gap-3">
                                        <div
                                            class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <x-heroicon-o-globe-alt class="w-4 h-4 text-amber-600" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <span
                                                class="text-[10px] font-light text-zinc-400 uppercase tracking-wide">Website</span>
                                            <a href="{{ route('track.exhibitor.website', $exhibitor) }}"
                                                target="_blank" rel="noopener"
                                                class="block text-sm text-zinc-700 font-light mt-0.5 hover:text-zinc-900 truncate">
                                                {{ parse_url($exhibitor->website, PHP_URL_HOST) ?? $exhibitor->website }}
                                            </a>
                                        </div>
                                    </div>
                                @endif

                                @if ($exhibitor->contact_person_name)
                                    <div class="flex items-start gap-3">
                                        <div
                                            class="w-8 h-8 bg-zinc-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <x-heroicon-o-user class="w-4 h-4 text-zinc-600" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <span
                                                class="text-[10px] font-light text-zinc-400 uppercase tracking-wide">Contact
                                                Person</span>
                                            <p class="text-sm text-zinc-700 font-light mt-0.5">
                                                {{ $exhibitor->contact_person_name }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Action Buttons -->
                            <div class="mt-6 pt-6 border-t border-zinc-100 space-y-3">
                                @if ($exhibitor->phone_number)
                                    <flux:button href="{{ route('track.exhibitor.call', $exhibitor) }}"
                                        variant="primary" class="w-full" icon="phone">
                                        Call Now
                                    </flux:button>
                                @endif

                                @if ($exhibitor->website)
                                    <flux:button href="{{ route('track.exhibitor.website', $exhibitor) }}"
                                        target="_blank" rel="noopener" variant="ghost" class="w-full">
                                        Visit Website
                                    </flux:button>
                                @endif
                            </div>
                        </div>

                        <!-- Brochure Download -->
                        @if ($exhibitor->brochure_path)
                            <div class="bg-zinc-900 rounded-xl p-6 text-center">
                                <div
                                    class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mx-auto mb-4">
                                    <x-heroicon-o-document-arrow-down class="w-6 h-6 text-white" />
                                </div>
                                <h3 class="text-sm font-normal text-white mb-1">Company Brochure</h3>
                                <p class="text-xs font-light text-zinc-400 mb-4">Download our detailed company profile
                                </p>
                                <flux:button href="{{ route('track.exhibitor.brochure', $exhibitor) }}"
                                    target="_blank" variant="primary">
                                    Download PDF
                                </flux:button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>
</x-layouts.front>
