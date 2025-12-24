<div class="mx-auto w-full max-w-7xl space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <flux:button variant="ghost" size="sm" icon="arrow-left" :href="route('companies.index')" wire:navigate />
            <div>
                <flux:heading size="xl">{{ $company->company_name }}</flux:heading>
                <flux:subheading class="mt-1">
                    <span class="text-zinc-500">{{ $company->registered_number }}</span>
                    @if ($company->submitted_at)
                        <span class="text-zinc-400">•</span>
                        <span class="text-green-600 dark:text-green-500">Submitted
                            {{ $company->submitted_at->format('M d, Y') }}</span>
                    @endif
                </flux:subheading>
            </div>
        </div>
        @if ($company->submitted_at)
            <flux:badge color="green" size="lg" icon="check-circle">Submitted</flux:badge>
        @endif
    </div>

    @if ($company->exhibitor)
        <!-- Tabs -->
        <flux:tab.group>
            <flux:tabs wire:model="tab">
                <flux:tab name="overview" icon="document-text">Overview</flux:tab>
                <flux:tab name="media" icon="photo">Media & Branding</flux:tab>
                <flux:tab name="requirements" icon="clipboard-document-list">Requirements</flux:tab>
                @if ($company->exhibitor->projects && $company->exhibitor->projects->count() > 0)
                    <flux:tab name="projects" icon="building-office">
                        Projects ({{ $company->exhibitor->projects->count() }})
                    </flux:tab>
                @endif
            </flux:tabs>

            <!-- Overview Tab Panel -->
            <flux:tab.panel name="overview">
                <flux:card>
                    <div class="grid gap-x-6 gap-y-4 sm:grid-cols-2 lg:grid-cols-3">
                        <!-- Company Info -->
                        <div>
                            <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Company Name</div>
                            <div class="mt-1 font-medium">{{ $company->company_name }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Registered Number</div>
                            <div class="mt-1 font-medium">{{ $company->registered_number }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Main Contact</div>
                            <div class="mt-1 font-medium">{{ $company->main_person_name }}</div>
                        </div>

                        <!-- Stall Info -->
                        @if ($company->stall_number)
                            <div>
                                <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Stall Number</div>
                                <div class="mt-1 font-medium">{{ $company->stall_number }}</div>
                            </div>
                        @endif
                        @if ($company->stall_type)
                            <div>
                                <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Stall Type</div>
                                <div class="mt-1 font-medium">{{ $company->stall_type }}</div>
                            </div>
                        @endif
                        @if ($company->stall_size)
                            <div>
                                <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Stall Size</div>
                                <div class="mt-1 font-medium">{{ $company->stall_size }}</div>
                            </div>
                        @endif

                        <!-- Exhibitor Details -->
                        @if ($company->exhibitor->city)
                            <div>
                                <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400">City</div>
                                <div class="mt-1 font-medium">{{ $company->exhibitor->city }}</div>
                            </div>
                        @endif
                        @if ($company->exhibitor->email)
                            <div>
                                <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Email</div>
                                <div class="mt-1 font-medium">{{ $company->exhibitor->email }}</div>
                            </div>
                        @endif
                        @if ($company->exhibitor->gst_number)
                            <div>
                                <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400">GST Number</div>
                                <div class="mt-1 font-medium">{{ $company->exhibitor->gst_number }}</div>
                            </div>
                        @endif
                        @if ($company->exhibitor->pan_number)
                            <div>
                                <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400">PAN Number</div>
                                <div class="mt-1 font-medium">{{ $company->exhibitor->pan_number }}</div>
                            </div>
                        @endif
                        @if ($company->exhibitor->website)
                            <div>
                                <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Website</div>
                                <div class="mt-1">
                                    <a href="{{ $company->exhibitor->website }}"
                                        class="font-medium text-blue-600 hover:underline dark:text-blue-400"
                                        target="_blank" rel="noopener">
                                        {{ Str::limit($company->exhibitor->website, 30) }}
                                    </a>
                                </div>
                            </div>
                        @endif

                        @if ($company->exhibitor->office_address)
                            <div class="sm:col-span-2 lg:col-span-3">
                                <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Office Address</div>
                                <div class="mt-1 font-medium">{{ $company->exhibitor->office_address }}</div>
                            </div>
                        @endif
                    </div>
                </flux:card>
            </flux:tab.panel>

            <!-- Media Tab Panel -->
            <flux:tab.panel name="media">
                <flux:card>
                    <div class="space-y-6">
                        @if ($company->exhibitor->logo_path)
                            <div>
                                <div class="mb-2 text-xs font-medium text-zinc-500 dark:text-zinc-400">Company Logo
                                </div>
                                <div class="flex items-center gap-3 rounded-lg border border-zinc-200 p-3 dark:border-zinc-700">
                                    <flux:icon.photo class="size-10 text-zinc-400 dark:text-zinc-500" />
                                    <div class="flex-1 min-w-0">
                                        <div class="truncate text-sm font-medium">{{ basename($company->exhibitor->logo_path) }}</div>
                                        @php
                                            try {
                                                $fileSize = Storage::disk('public')->size($company->exhibitor->logo_path);
                                            } catch (\Exception $e) {
                                                $fileSize = null;
                                            }
                                        @endphp
                                        @if ($fileSize)
                                            <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                                {{ number_format($fileSize / 1024, 2) }} KB
                                            </div>
                                        @endif
                                    </div>
                                    <a href="{{ Storage::url($company->exhibitor->logo_path) }}"
                                        download="{{ basename($company->exhibitor->logo_path) }}">
                                        <flux:button variant="primary" size="sm" icon="arrow-down-tray">
                                            Download
                                        </flux:button>
                                    </a>
                                </div>
                            </div>
                        @endif

                        @if ($company->exhibitor->brochure_path)
                            <div>
                                <div class="mb-2 text-xs font-medium text-zinc-500 dark:text-zinc-400">Brochure</div>
                                <flux:button variant="primary" size="sm" icon="document-text"
                                    href="{{ Storage::url($company->exhibitor->brochure_path) }}" target="_blank">
                                    Download Brochure
                                </flux:button>
                            </div>
                        @endif

                        @if ($company->exhibitor->photos && count($company->exhibitor->photos) > 0)
                            <div>
                                <div class="mb-2 text-xs font-medium text-zinc-500 dark:text-zinc-400">
                                    Company Photos ({{ count($company->exhibitor->photos) }})
                                </div>
                                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                                    @foreach ($company->exhibitor->photos as $photo)
                                        <img src="{{ Storage::url($photo) }}" alt="Company photo {{ $loop->iteration }}"
                                            class="aspect-square w-full rounded-lg border border-zinc-200 object-cover shadow-sm transition hover:shadow-md dark:border-zinc-700">
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if ($company->exhibitor->video_url)
                            <div>
                                <div class="mb-2 text-xs font-medium text-zinc-500 dark:text-zinc-400">Video URL</div>
                                <a href="{{ $company->exhibitor->video_url }}"
                                    class="inline-flex items-center gap-2 text-sm text-blue-600 hover:underline dark:text-blue-400"
                                    target="_blank" rel="noopener">
                                    <flux:icon.play-circle variant="micro" />
                                    {{ $company->exhibitor->video_url }}
                                </a>
                            </div>
                        @endif

                        @if ($company->exhibitor->social_media_links && count($company->exhibitor->social_media_links) > 0)
                            <div>
                                <div class="mb-2 text-xs font-medium text-zinc-500 dark:text-zinc-400">Social Media
                                </div>
                                <div class="grid gap-2 sm:grid-cols-2">
                                    @foreach ($company->exhibitor->social_media_links as $platform => $url)
                                        <a href="{{ $url }}"
                                            class="flex items-center gap-2 rounded-lg border border-zinc-200 p-2.5 transition hover:border-zinc-300 hover:bg-zinc-50 dark:border-zinc-700 dark:hover:border-zinc-600 dark:hover:bg-zinc-800"
                                            target="_blank" rel="noopener">
                                            <flux:icon.link variant="micro" class="text-zinc-500 dark:text-zinc-400" />
                                            <div class="flex-1 overflow-hidden">
                                                <div class="text-sm font-medium capitalize">{{ $platform }}</div>
                                                <div class="truncate text-xs text-zinc-500 dark:text-zinc-400">
                                                    {{ $url }}
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </flux:card>
            </flux:tab.panel>

            <!-- Requirements Tab Panel -->
            <flux:tab.panel name="requirements">
                <flux:card>
                    <div class="grid gap-x-6 gap-y-4 sm:grid-cols-2 lg:grid-cols-3">
                        @if ($company->exhibitor->facia_name)
                            <div>
                                <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Facia Name</div>
                                <div class="mt-1 font-medium">{{ $company->exhibitor->facia_name }}</div>
                            </div>
                        @endif
                        @if ($company->exhibitor->momento_name)
                            <div>
                                <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Momento Name</div>
                                <div class="mt-1 font-medium">{{ $company->exhibitor->momento_name }}</div>
                            </div>
                        @endif
                        @if ($company->exhibitor->extra_furniture_details)
                            <div class="sm:col-span-2 lg:col-span-3">
                                <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Extra Furniture
                                    Details</div>
                                <div class="mt-1 font-medium">{{ $company->exhibitor->extra_furniture_details }}
                                </div>
                            </div>
                        @endif
                        @if ($company->exhibitor->exhibitor_passes_details)
                            <div class="sm:col-span-2 lg:col-span-3">
                                <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Exhibitor Passes
                                    Details</div>
                                <div class="mt-1 font-medium">{{ $company->exhibitor->exhibitor_passes_details }}
                                </div>
                            </div>
                        @endif
                        @if ($company->exhibitor->car_pass_details)
                            <div class="sm:col-span-2 lg:col-span-3">
                                <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Car Pass Details
                                </div>
                                <div class="mt-1 font-medium">{{ $company->exhibitor->car_pass_details }}</div>
                            </div>
                        @endif
                        @if ($company->exhibitor->additional_details)
                            <div class="sm:col-span-2 lg:col-span-3">
                                <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Additional Details
                                </div>
                                <div class="mt-1 font-medium">{{ $company->exhibitor->additional_details }}</div>
                            </div>
                        @endif
                    </div>
                </flux:card>
            </flux:tab.panel>

            <!-- Projects Tab Panel -->
            @if ($company->exhibitor->projects && $company->exhibitor->projects->count() > 0)
                <flux:tab.panel name="projects">
                <!-- Split Layout for Projects -->
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
                    <!-- Projects List (Left Side) -->
                    <div class="lg:col-span-4">
                        <flux:card class="h-full">
                            <div class="space-y-2">
                                <div class="mb-4 text-sm font-semibold text-zinc-900 dark:text-white">
                                    Projects ({{ $company->exhibitor->projects->count() }})
                                </div>
                                <div class="space-y-2">
                                    @foreach ($company->exhibitor->projects as $index => $project)
                                        <button type="button" wire:click="selectProject({{ $index }})"
                                            class="w-full rounded-lg border p-3 text-left transition
                                                {{ $selectedProjectIndex === $index ? 'border-blue-500 bg-blue-50 dark:border-blue-600 dark:bg-blue-950/30' : 'border-zinc-200 hover:border-zinc-300 hover:bg-zinc-50 dark:border-zinc-700 dark:hover:border-zinc-600 dark:hover:bg-zinc-800' }}">
                                            <div class="flex items-start gap-3">
                                                @if ($project->logo_path)
                                                    <img src="{{ Storage::url($project->logo_path) }}"
                                                        alt="{{ $project->name }} logo"
                                                        class="h-10 w-10 rounded border border-zinc-200 bg-white object-cover dark:border-zinc-700 dark:bg-zinc-800">
                                                @endif
                                                <div class="min-w-0 flex-1">
                                                    <div
                                                        class="truncate text-sm font-medium {{ $selectedProjectIndex === $index ? 'text-blue-900 dark:text-blue-100' : 'text-zinc-900 dark:text-white' }}">
                                                        {{ $project->name }}
                                                    </div>
                                                    @if ($project->category)
                                                        <div class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">
                                                            {{ $project->category }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </flux:card>
                    </div>

                    <!-- Project Details (Right Side) -->
                    <div class="lg:col-span-8">
                        @if ($selectedProjectIndex !== null && isset($company->exhibitor->projects[$selectedProjectIndex]))
                            @php
                                $project = $company->exhibitor->projects[$selectedProjectIndex];
                            @endphp
                            <flux:card wire:key="project-{{ $selectedProjectIndex }}">
                                <div class="space-y-6">
                                    <!-- Project Header -->
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <flux:heading size="lg">{{ $project->name }}</flux:heading>
                                            @if ($project->category)
                                                <flux:badge color="zinc" class="mt-2">{{ $project->category }}
                                                </flux:badge>
                                            @endif
                                        </div>
                                        @if ($project->logo_path)
                                            <img src="{{ Storage::url($project->logo_path) }}"
                                                alt="{{ $project->name }} logo"
                                                class="h-16 w-16 rounded-lg border border-zinc-200 bg-white object-cover p-1 dark:border-zinc-700 dark:bg-zinc-800">
                                        @endif
                                    </div>

                                    <!-- Project Details Grid -->
                                    <div class="grid gap-x-6 gap-y-4 sm:grid-cols-2 lg:grid-cols-3">
                                        @if ($project->area)
                                            <div>
                                                <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Area
                                                </div>
                                                <div class="mt-1 font-medium">{{ $project->area }}</div>
                                            </div>
                                        @endif
                                        @if ($project->sq_ft)
                                            <div>
                                                <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Size
                                                </div>
                                                <div class="mt-1 font-medium">{{ $project->sq_ft }} sq. ft.</div>
                                            </div>
                                        @endif
                                        @if ($project->budget_range)
                                            <div>
                                                <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400">
                                                    Budget Range</div>
                                                <div class="mt-1 font-medium">{{ $project->budget_range }}</div>
                                            </div>
                                        @endif
                                        @if ($project->handover_date)
                                            <div>
                                                <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400">
                                                    Handover Date</div>
                                                <div class="mt-1 font-medium">{{ $project->handover_date }}</div>
                                            </div>
                                        @endif
                                        @if ($project->status)
                                            <div>
                                                <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400">
                                                    Status</div>
                                                <div class="mt-1 font-medium">{{ $project->status }}</div>
                                            </div>
                                        @endif
                                        @if ($project->contact_person)
                                            <div>
                                                <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400">
                                                    Contact Person</div>
                                                <div class="mt-1 font-medium">{{ $project->contact_person }}</div>
                                            </div>
                                        @endif
                                        @if ($project->usp)
                                            <div class="sm:col-span-2 lg:col-span-3">
                                                <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400">
                                                    Unique Selling Point</div>
                                                <div class="mt-1 font-medium">{{ $project->usp }}</div>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Media -->
                                    @if ($project->video_url || $project->pdf_path)
                                        <div class="flex flex-wrap gap-3 border-t border-zinc-200 pt-4 dark:border-zinc-700">
                                            @if ($project->video_url)
                                                <a href="{{ $project->video_url }}"
                                                    class="inline-flex items-center gap-2 text-sm text-blue-600 hover:underline dark:text-blue-400"
                                                    target="_blank" rel="noopener">
                                                    <flux:icon.play-circle variant="micro" />
                                                    Watch Video
                                                </a>
                                            @endif
                                            @if ($project->pdf_path)
                                                <flux:button variant="primary" size="sm" icon="document-text"
                                                    href="{{ Storage::url($project->pdf_path) }}" target="_blank">
                                                    Download PDF
                                                </flux:button>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </flux:card>
                        @else
                            <flux:card>
                                <div class="py-12 text-center">
                                    <flux:icon.building-office class="mx-auto mb-3 text-zinc-300 dark:text-zinc-600"
                                        variant="outline" />
                                    <flux:subheading>Select a project to view details</flux:subheading>
                                </div>
                            </flux:card>
                        @endif
                    </div>
                </div>
                </flux:tab.panel>
            @endif
        </flux:tab.group>
    @else
        <!-- No Submission Data -->
        <flux:card>
            <div class="py-12 text-center">
                <flux:icon.document-text class="mx-auto mb-4 text-zinc-400" variant="outline" />
                <flux:heading size="lg" class="mb-2">No submission data available</flux:heading>
                <flux:subheading>The form has been marked as submitted but no data was found.
                </flux:subheading>
            </div>
        </flux:card>
    @endif
</div>
