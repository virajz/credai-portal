<x-layouts.front :title="$exhibitor->brand_name . ' - ' . config('app.name')" :back-link="route('public.exhibitors')" back-text="Back to Exhibitors" body-class="bg-zinc-50">

    <!-- Hero Section with Banner -->
    <section>
        <div class="relative h-64 sm:h-80 lg:h-96 bg-gradient-to-br from-zinc-800 to-zinc-900 overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                    <defs>
                        <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                            <path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5" />
                        </pattern>
                    </defs>
                    <rect width="100" height="100" fill="url(#grid)" />
                </svg>
            </div>

            <!-- Hero Content -->
            <div class="absolute inset-0 flex items-end">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full pb-8">
                    <!-- Breadcrumb -->
                    <nav class="mb-4">
                        <ol class="flex items-center gap-2 text-xs font-light">
                            <li>
                                <a href="{{ route('home') }}"
                                    class="text-zinc-400 hover:text-white transition-colors">Home</a>
                            </li>
                            <li class="text-zinc-600">/</li>
                            <li>
                                <a href="{{ route('public.exhibitors') }}"
                                    class="text-zinc-400 hover:text-white transition-colors">Exhibitors</a>
                            </li>
                            <li class="text-zinc-600">/</li>
                            <li class="text-white">{{ $exhibitor->brand_name }}</li>
                        </ol>
                    </nav>

                    <div class="flex items-end gap-6">
                        <!-- Logo Box -->
                        <div class="w-20 h-20 sm:w-24 sm:h-24 flex items-center justify-center flex-shrink-0">
                            @if ($exhibitor->preview_logo ?? $exhibitor->logo_path)
                                <img src="{{ Storage::url($exhibitor->preview_logo ?? $exhibitor->logo_path) }}"
                                    alt="{{ $exhibitor->brand_name }}" class="w-full h-full object-contain" />
                            @else
                                <x-heroicon-o-building-office class="w-10 h-10 text-zinc-400" />
                            @endif
                        </div>

                        <!-- Info -->
                        <div class="flex-1 min-w-0 pb-1">
                            @if ($exhibitor->company?->stall_number)
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 text-[10px] font-normal bg-white/20 text-white rounded-full mb-2">
                                    Stall {{ $exhibitor->company->stall_number }}
                                </span>
                            @endif
                            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-light text-white truncate">
                                {{ $exhibitor->brand_name }}
                            </h1>
                            @if ($exhibitor->city)
                                <p class="flex items-center gap-1.5 text-sm font-light text-zinc-300 mt-1">
                                    <x-heroicon-o-map-pin class="w-3.5 h-3.5" />
                                    {{ $exhibitor->city }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
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
                    <div class="bg-white rounded-xl p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-sm font-medium text-zinc-900">
                                Projects
                            </h2>
                            @if ($exhibitor->projects->count() > 0)
                                <span class="text-xs font-light text-zinc-400">{{ $exhibitor->projects->count() }}
                                    {{ Str::plural('project', $exhibitor->projects->count()) }}</span>
                            @endif
                        </div>

                        @if ($exhibitor->projects->count() > 0)
                            <div class="space-y-4">
                                @foreach ($exhibitor->projects as $project)
                                    <a href="{{ route('project.show', $project) }}"
                                        class="group block bg-zinc-50 hover:bg-zinc-100 rounded-xl p-5 transition-colors">
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 mb-2">
                                                    <h3
                                                        class="text-sm font-normal text-zinc-900 truncate group-hover:text-zinc-700">
                                                        {{ $project->name }}</h3>
                                                    @if ($project->status)
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 text-[10px] font-normal rounded-full
                                                            {{ $project->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                                            {{ $project->status === 'ongoing' ? 'bg-blue-100 text-blue-700' : '' }}
                                                            {{ $project->status === 'upcoming' ? 'bg-amber-100 text-amber-700' : '' }}
                                                            {{ !in_array($project->status, ['completed', 'ongoing', 'upcoming']) ? 'bg-zinc-200 text-zinc-600' : '' }}">
                                                            {{ ucfirst($project->status) }}
                                                        </span>
                                                    @endif
                                                </div>

                                                <div
                                                    class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-zinc-500">
                                                    @if ($project->area)
                                                        <span class="flex items-center gap-1">
                                                            <x-heroicon-o-map-pin class="w-3.5 h-3.5 text-zinc-400" />
                                                            <span class="font-light">{{ $project->area }}</span>
                                                        </span>
                                                    @endif
                                                    @if ($project->category)
                                                        <span class="flex items-center gap-1">
                                                            <x-heroicon-o-building-office
                                                                class="w-3.5 h-3.5 text-zinc-400" />
                                                            <span class="font-light">{{ $project->category }}</span>
                                                        </span>
                                                    @endif
                                                    @if ($project->budget_range)
                                                        <span class="flex items-center gap-1">
                                                            <x-heroicon-o-currency-rupee
                                                                class="w-3.5 h-3.5 text-zinc-400" />
                                                            <span
                                                                class="font-light">{{ $project->budget_range }}</span>
                                                        </span>
                                                    @endif
                                                    @if ($project->handover_date)
                                                        <span class="flex items-center gap-1">
                                                            <x-heroicon-o-calendar class="w-3.5 h-3.5 text-zinc-400" />
                                                            <span
                                                                class="font-light">{{ $project->handover_date }}</span>
                                                        </span>
                                                    @endif
                                                </div>

                                                @if ($project->usp)
                                                    <p class="mt-3 text-xs text-zinc-500 font-light line-clamp-2">
                                                        {{ $project->usp }}</p>
                                                @endif
                                            </div>

                                            <div class="flex items-center gap-2 flex-shrink-0">
                                                @if ($project->pdf_path)
                                                    <span
                                                        onclick="event.preventDefault(); event.stopPropagation(); window.open('{{ route('track.project.brochure', $project) }}', '_blank');"
                                                        class="inline-flex items-center justify-center w-9 h-9 bg-zinc-200 hover:bg-zinc-300 text-zinc-600 rounded-lg transition-colors"
                                                        title="Download PDF">
                                                        <x-heroicon-o-document-arrow-down class="w-4 h-4" />
                                                    </span>
                                                @endif
                                                <span
                                                    class="inline-flex items-center justify-center w-9 h-9 text-zinc-400 group-hover:text-zinc-600 transition-colors">
                                                    <x-heroicon-o-chevron-right class="w-4 h-4" />
                                                </span>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <!-- Empty State -->
                            <div class="text-center py-8">
                                <div
                                    class="w-12 h-12 bg-zinc-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                                    <x-heroicon-o-building-office class="w-6 h-6 text-zinc-400" />
                                </div>
                                <p class="text-sm font-light text-zinc-500 mb-1">No projects listed yet</p>
                                <p class="text-xs font-light text-zinc-400">Check back soon for updates</p>
                            </div>
                        @endif
                    </div>

                    <!-- Social Media & Connect -->
                    @if ($exhibitor->social_media_links && count($exhibitor->social_media_links) > 0)
                        <div class="bg-white rounded-xl p-6 shadow-sm">
                            <h2 class="text-sm font-medium text-zinc-900 mb-4">Connect</h2>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($exhibitor->social_media_links as $platform => $url)
                                    @if ($url)
                                        <a href="{{ $url }}" target="_blank" rel="noopener"
                                            class="inline-flex items-center px-4 py-2 text-xs font-light text-zinc-600 bg-zinc-100 hover:bg-zinc-200 rounded-full transition-colors">
                                            {{ ucfirst($platform) }}
                                            <x-heroicon-o-arrow-top-right-on-square class="w-3 h-3 ml-2" />
                                        </a>
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
                                            class="w-8 h-8 bg-emerald-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <x-heroicon-o-phone class="w-4 h-4 text-emerald-600" />
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
                                    <a href="{{ route('track.exhibitor.call', $exhibitor) }}"
                                        class="flex items-center justify-center gap-2 w-full px-4 py-2.5 text-sm font-normal text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition-colors">
                                        <x-heroicon-o-phone class="w-4 h-4" />
                                        Call Now
                                    </a>
                                @endif

                                @if ($exhibitor->website)
                                    <a href="{{ route('track.exhibitor.website', $exhibitor) }}" target="_blank"
                                        rel="noopener"
                                        class="flex items-center justify-center gap-2 w-full px-4 py-2.5 text-sm font-normal text-zinc-700 bg-zinc-100 hover:bg-zinc-200 rounded-lg transition-colors">
                                        <x-heroicon-o-arrow-top-right-on-square class="w-4 h-4" />
                                        Visit Website
                                    </a>
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
                                <a href="{{ route('track.exhibitor.brochure', $exhibitor) }}" target="_blank"
                                    class="inline-flex items-center px-5 py-2.5 text-sm font-normal text-zinc-900 bg-white hover:bg-zinc-100 rounded-lg transition-colors">
                                    Download PDF
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>
</x-layouts.front>
