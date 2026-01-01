<x-layouts.front :title="$project->name . ' - ' . config('app.name')" :back-link="route('exhibitor.show', $project->exhibitor)" :back-text="'Back to ' . $project->exhibitor->brand_name" body-class="bg-zinc-50">

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
                        <a href="{{ route('home') }}#exhibitors"
                            class="text-zinc-500 hover:text-zinc-900 transition-colors">Exhibitors</a>
                    </li>
                    <li class="text-zinc-400">/</li>
                    <li>
                        <a href="{{ route('exhibitor.show', $project->exhibitor) }}"
                            class="text-zinc-500 hover:text-zinc-900 transition-colors">{{ $project->exhibitor->brand_name }}</a>
                    </li>
                    <li class="text-zinc-400">/</li>
                    <li class="text-zinc-900">{{ $project->name }}</li>
                </ol>
            </nav>

            <div class="flex items-center justify-between gap-6">
                <div class="flex items-center gap-4 sm:gap-6 flex-1 min-w-0">
                    <!-- Project Logo/Icon -->
                    <div class="w-16 h-16 sm:w-20 sm:h-20 flex items-center justify-center flex-shrink-0">
                        @if ($project->logo_path)
                            <img src="{{ Storage::url($project->logo_path) }}" alt="{{ $project->name }}"
                                class="w-full h-full object-contain" />
                        @else
                            <x-heroicon-o-building-office-2 class="w-8 h-8 text-zinc-400" />
                        @endif
                    </div>

                    <!-- Info -->
                    <div class="flex-1 min-w-0">
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-light text-zinc-900">
                            {{ $project->name }}
                            <span class="text-xs sm:text-sm font-light text-zinc-500">
                                by {{ $project->exhibitor->brand_name }}
                            </span>
                        </h1>
                        <div class="flex items-center gap-3 flex-wrap mt-1">
                            @if ($project->area)
                                <span class="flex items-center gap-1.5 text-sm font-light text-zinc-600">
                                    <x-heroicon-o-map-pin class="w-3.5 h-3.5" />
                                    {{ $project->area }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Badges on the right -->
                <div class="flex items-center gap-2 flex-shrink-0">
                    @if ($project->status)
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 text-[10px] font-normal rounded-full
                            {{ $project->status === 'completed' ? 'bg-teal-600 text-white' : '' }}
                            {{ $project->status === 'ongoing' ? 'bg-blue-600 text-white' : '' }}
                            {{ $project->status === 'upcoming' ? 'bg-amber-600 text-white' : '' }}
                            {{ !in_array($project->status, ['completed', 'ongoing', 'upcoming']) ? 'bg-zinc-900 text-white' : '' }}">
                            {{ ucfirst($project->status) }}
                        </span>
                    @endif
                    @if ($project->category)
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 text-[10px] font-normal bg-zinc-900 text-white rounded-full">
                            {{ $project->category }}
                        </span>
                    @endif
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
                    <!-- Project Overview -->
                    @if ($project->usp)
                        <div class="bg-white rounded-xl p-6 shadow-sm">
                            <h2 class="text-sm font-medium text-zinc-900 mb-4">About This Project</h2>
                            <p class="text-sm text-zinc-600 font-light leading-relaxed">
                                {{ $project->usp }}
                            </p>
                        </div>
                    @endif

                    <!-- Key Details Grid -->
                    <div class="bg-white rounded-xl p-6 shadow-sm">
                        <h2 class="text-sm font-medium text-zinc-900 mb-6">Project Details</h2>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                            @if ($project->category)
                                <div class="bg-zinc-50 rounded-lg p-4">
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                                            <x-heroicon-o-building-office class="w-4 h-4 text-blue-600" />
                                        </div>
                                    </div>
                                    <span
                                        class="text-[10px] font-light text-zinc-400 uppercase tracking-wide">Type</span>
                                    <p class="text-sm text-zinc-900 font-normal mt-0.5">{{ $project->category }}</p>
                                </div>
                            @endif

                            @if ($project->area)
                                <div class="bg-zinc-50 rounded-lg p-4">
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="w-8 h-8 bg-teal-50 rounded-lg flex items-center justify-center">
                                            <x-heroicon-o-map-pin class="w-4 h-4 text-teal-600" />
                                        </div>
                                    </div>
                                    <span
                                        class="text-[10px] font-light text-zinc-400 uppercase tracking-wide">Location</span>
                                    <p class="text-sm text-zinc-900 font-normal mt-0.5">{{ $project->area }}</p>
                                </div>
                            @endif

                            @if ($project->sq_ft)
                                <div class="bg-zinc-50 rounded-lg p-4">
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="w-8 h-8 bg-purple-50 rounded-lg flex items-center justify-center">
                                            <x-heroicon-o-square-3-stack-3d class="w-4 h-4 text-purple-600" />
                                        </div>
                                    </div>
                                    <span
                                        class="text-[10px] font-light text-zinc-400 uppercase tracking-wide">Area</span>
                                    <p class="text-sm text-zinc-900 font-normal mt-0.5">{{ $project->sq_ft }} sq.ft</p>
                                </div>
                            @endif

                            @if ($project->budget_range)
                                <div class="bg-zinc-50 rounded-lg p-4">
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center">
                                            <x-heroicon-o-currency-rupee class="w-4 h-4 text-amber-600" />
                                        </div>
                                    </div>
                                    <span
                                        class="text-[10px] font-light text-zinc-400 uppercase tracking-wide">Budget</span>
                                    <p class="text-sm text-zinc-900 font-normal mt-0.5">{{ $project->budget_range }}
                                    </p>
                                </div>
                            @endif

                            @if ($project->handover_date)
                                <div class="bg-zinc-50 rounded-lg p-4">
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="w-8 h-8 bg-rose-50 rounded-lg flex items-center justify-center">
                                            <x-heroicon-o-calendar class="w-4 h-4 text-rose-600" />
                                        </div>
                                    </div>
                                    <span
                                        class="text-[10px] font-light text-zinc-400 uppercase tracking-wide">Handover</span>
                                    <p class="text-sm text-zinc-900 font-normal mt-0.5">{{ $project->handover_date }}
                                    </p>
                                </div>
                            @endif

                            @if ($project->status)
                                <div class="bg-zinc-50 rounded-lg p-4">
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="w-8 h-8 bg-zinc-100 rounded-lg flex items-center justify-center">
                                            <x-heroicon-o-clock class="w-4 h-4 text-zinc-600" />
                                        </div>
                                    </div>
                                    <span
                                        class="text-[10px] font-light text-zinc-400 uppercase tracking-wide">Status</span>
                                    <p class="text-sm text-zinc-900 font-normal mt-0.5">
                                        {{ ucfirst($project->status) }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Video Section -->
                    @if ($project->video_url)
                        <div class="bg-white rounded-xl p-6 shadow-sm">
                            <h2 class="text-sm font-medium text-zinc-900 mb-4">Project Video</h2>
                            <div class="aspect-video rounded-lg overflow-hidden bg-zinc-100">
                                @if (str_contains($project->video_url, 'youtube') || str_contains($project->video_url, 'youtu.be'))
                                    @php
                                        preg_match(
                                            '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/',
                                            $project->video_url,
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
                                    <a href="{{ $project->video_url }}" target="_blank" rel="noopener"
                                        class="flex items-center justify-center h-full text-zinc-500 hover:text-zinc-700 transition-colors">
                                        <div class="text-center">
                                            <x-heroicon-o-play-circle class="w-12 h-12 mx-auto mb-2" />
                                            <span class="text-sm font-light">Watch Video</span>
                                        </div>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right Column - Contact Sidebar -->
                <div class="lg:col-span-1">
                    <div class="lg:sticky lg:top-28 space-y-6">
                        <!-- Developer Card -->
                        <div class="bg-white rounded-xl p-6 shadow-sm">
                            <h2 class="text-sm font-medium text-zinc-900 mb-5">Developer</h2>

                            <a href="{{ route('exhibitor.show', $project->exhibitor) }}"
                                class="flex items-center gap-4 mb-6 group">
                                <div class="w-14 h-14 flex items-center justify-center flex-shrink-0">
                                    @if ($project->exhibitor->preview_logo ?? $project->exhibitor->logo_path)
                                        <img src="{{ Storage::url($project->exhibitor->preview_logo ?? $project->exhibitor->logo_path) }}"
                                            alt="{{ $project->exhibitor->brand_name }}"
                                            class="w-full h-full object-contain" />
                                    @else
                                        <x-heroicon-o-building-office class="w-6 h-6 text-zinc-400" />
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-normal text-zinc-900 group-hover:text-zinc-700 truncate">
                                        {{ $project->exhibitor->brand_name }}
                                    </p>
                                    <p class="text-xs font-light text-zinc-500">View all projects</p>
                                </div>
                                <x-heroicon-o-chevron-right
                                    class="w-4 h-4 text-zinc-400 group-hover:text-zinc-600 transition-colors" />
                            </a>

                            <div class="space-y-4">
                                @if ($project->contact_person)
                                    <div class="flex items-start gap-3">
                                        <div
                                            class="w-8 h-8 bg-zinc-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <x-heroicon-o-user class="w-4 h-4 text-zinc-600" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <span
                                                class="text-[10px] font-light text-zinc-400 uppercase tracking-wide">Contact</span>
                                            <p class="text-sm text-zinc-700 font-light mt-0.5">
                                                {{ $project->contact_person }}</p>
                                        </div>
                                    </div>
                                @endif

                                @if ($project->exhibitor->phone_number)
                                    <div class="flex items-start gap-3">
                                        <div
                                            class="w-8 h-8 bg-teal-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <x-heroicon-o-phone class="w-4 h-4 text-teal-600" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <span
                                                class="text-[10px] font-light text-zinc-400 uppercase tracking-wide">Phone</span>
                                            <p class="text-sm text-zinc-700 font-light mt-0.5">
                                                {{ $project->exhibitor->phone_number }}</p>
                                        </div>
                                    </div>
                                @endif

                                @if ($project->exhibitor->email)
                                    <div class="flex items-start gap-3">
                                        <div
                                            class="w-8 h-8 bg-purple-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <x-heroicon-o-envelope class="w-4 h-4 text-purple-600" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <span
                                                class="text-[10px] font-light text-zinc-400 uppercase tracking-wide">Email</span>
                                            <p class="text-sm text-zinc-700 font-light mt-0.5 break-all">
                                                {{ $project->exhibitor->email }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Action Buttons -->
                            <div class="mt-6 pt-6 border-t border-zinc-100 space-y-3">
                                @if ($project->exhibitor->phone_number)
                                    <flux:button href="{{ route('track.project.call', $project) }}" variant="primary"
                                        class="w-full" icon="phone">
                                        Call Now
                                    </flux:button>
                                @endif

                                <flux:button href="{{ route('exhibitor.show', $project->exhibitor) }}"
                                    variant="ghost" class="w-full" icon="building-office">
                                    View Developer
                                </flux:button>
                            </div>
                        </div>

                        <!-- Brochure Download -->
                        @if ($project->pdf_path)
                            <div class="bg-zinc-900 rounded-xl p-6 text-center">
                                <div
                                    class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mx-auto mb-4">
                                    <x-heroicon-o-document-arrow-down class="w-6 h-6 text-white" />
                                </div>
                                <h3 class="text-sm font-normal text-white mb-1">Project Brochure</h3>
                                <p class="text-xs font-light text-zinc-400 mb-4">Download detailed project information
                                </p>
                                <flux:button href="{{ route('track.project.brochure', $project) }}" target="_blank">
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
