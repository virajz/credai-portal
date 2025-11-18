<div class="mx-auto w-full max-w-7xl">
    <!-- Header -->
    <div class="mb-8">
        <flux:button variant="ghost" size="sm" icon="arrow-left" :href="route('companies.index')" wire:navigate
            class="mb-4">
            Back to Companies
        </flux:button>

        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div>
                <flux:heading size="xl" class="mb-2">{{ $company->company_name }}</flux:heading>
                <flux:subheading>Form Submission Details</flux:subheading>
            </div>
            @if ($company->submitted_at)
                <flux:badge color="green" size="lg" icon="check-circle">
                    Submitted {{ $company->submitted_at->format('M d, Y') }}
                </flux:badge>
            @endif
        </div>
    </div>

    <div class="space-y-8">
        <!-- Company Information -->
        <flux:card>
            <div class="border-b border-zinc-200 pb-4 dark:border-zinc-700">
                <flux:heading size="lg">Company Information</flux:heading>
            </div>

            <div class="grid gap-6 pt-6 md:grid-cols-2 lg:grid-cols-3">
                <div>
                    <flux:text class="mb-1.5 text-sm text-zinc-500 dark:text-zinc-400">Company Name</flux:text>
                    <flux:text class="font-medium">{{ $company->company_name }}</flux:text>
                </div>
                <div>
                    <flux:text class="mb-1.5 text-sm text-zinc-500 dark:text-zinc-400">Registered Number</flux:text>
                    <flux:text class="font-medium">{{ $company->registered_number }}</flux:text>
                </div>
                <div>
                    <flux:text class="mb-1.5 text-sm text-zinc-500 dark:text-zinc-400">Main Contact Person</flux:text>
                    <flux:text class="font-medium">{{ $company->main_person_name }}</flux:text>
                </div>
                @if ($company->stall_number)
                    <div>
                        <flux:text class="mb-1.5 text-sm text-zinc-500 dark:text-zinc-400">Stall Number</flux:text>
                        <flux:text class="font-medium">{{ $company->stall_number }}</flux:text>
                    </div>
                @endif
                @if ($company->stall_type)
                    <div>
                        <flux:text class="mb-1.5 text-sm text-zinc-500 dark:text-zinc-400">Stall Type</flux:text>
                        <flux:text class="font-medium">{{ $company->stall_type }}</flux:text>
                    </div>
                @endif
                @if ($company->stall_size)
                    <div>
                        <flux:text class="mb-1.5 text-sm text-zinc-500 dark:text-zinc-400">Stall Size</flux:text>
                        <flux:text class="font-medium">{{ $company->stall_size }}</flux:text>
                    </div>
                @endif
            </div>
        </flux:card>

        @if ($company->exhibitor)
            <!-- Exhibitor Details -->
            <flux:card>
                <div class="border-b border-zinc-200 pb-4 dark:border-zinc-700">
                    <flux:heading size="lg">Exhibitor Details</flux:heading>
                </div>

                <div class="grid gap-6 pt-6 md:grid-cols-2 lg:grid-cols-3">
                    @if ($company->exhibitor->office_address)
                        <div class="lg:col-span-3">
                            <flux:text class="mb-1.5 text-sm text-zinc-500 dark:text-zinc-400">Office Address
                            </flux:text>
                            <flux:text class="font-medium">{{ $company->exhibitor->office_address }}</flux:text>
                        </div>
                    @endif
                    @if ($company->exhibitor->city)
                        <div>
                            <flux:text class="mb-1.5 text-sm text-zinc-500 dark:text-zinc-400">City</flux:text>
                            <flux:text class="font-medium">{{ $company->exhibitor->city }}</flux:text>
                        </div>
                    @endif
                    @if ($company->exhibitor->email)
                        <div>
                            <flux:text class="mb-1.5 text-sm text-zinc-500 dark:text-zinc-400">Email</flux:text>
                            <flux:text class="font-medium">{{ $company->exhibitor->email }}</flux:text>
                        </div>
                    @endif
                    @if ($company->exhibitor->gst_number)
                        <div>
                            <flux:text class="mb-1.5 text-sm text-zinc-500 dark:text-zinc-400">GST Number</flux:text>
                            <flux:text class="font-medium">{{ $company->exhibitor->gst_number }}</flux:text>
                        </div>
                    @endif
                    @if ($company->exhibitor->pan_number)
                        <div>
                            <flux:text class="mb-1.5 text-sm text-zinc-500 dark:text-zinc-400">PAN Number</flux:text>
                            <flux:text class="font-medium">{{ $company->exhibitor->pan_number }}</flux:text>
                        </div>
                    @endif
                    @if ($company->exhibitor->website)
                        <div>
                            <flux:text class="mb-1.5 text-sm text-zinc-500 dark:text-zinc-400">Website</flux:text>
                            <flux:text class="font-medium">
                                <a href="{{ $company->exhibitor->website }}"
                                    class="text-blue-600 hover:underline dark:text-blue-400" target="_blank"
                                    rel="noopener">
                                    {{ $company->exhibitor->website }}
                                </a>
                            </flux:text>
                        </div>
                    @endif
                </div>
            </flux:card>

            <!-- Media & Branding -->
            <flux:card>
                <div class="border-b border-zinc-200 pb-4 dark:border-zinc-700">
                    <flux:heading size="lg">Media & Branding</flux:heading>
                </div>

                <div class="space-y-8 pt-6">
                    @if ($company->exhibitor->logo_path)
                        <div>
                            <flux:text class="mb-3 text-sm text-zinc-500 dark:text-zinc-400">Company Logo</flux:text>
                            <img src="{{ Storage::url($company->exhibitor->logo_path) }}"
                                alt="{{ $company->company_name }} logo"
                                class="h-24 w-auto rounded-lg border border-zinc-200 bg-white p-2 shadow-sm dark:border-zinc-700 dark:bg-zinc-800">
                        </div>
                    @endif

                    @if ($company->exhibitor->brochure_path)
                        <div>
                            <flux:text class="mb-3 text-sm text-zinc-500 dark:text-zinc-400">Brochure</flux:text>
                            <flux:button variant="primary" icon="document-text"
                                href="{{ Storage::url($company->exhibitor->brochure_path) }}" target="_blank">
                                Download Brochure
                            </flux:button>
                        </div>
                    @endif

                    @if ($company->exhibitor->photos && count($company->exhibitor->photos) > 0)
                        <div>
                            <flux:text class="mb-3 text-sm text-zinc-500 dark:text-zinc-400">Company Photos
                                ({{ count($company->exhibitor->photos) }})</flux:text>
                            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                                @foreach ($company->exhibitor->photos as $photo)
                                    <img src="{{ Storage::url($photo) }}" alt="Company photo {{ $loop->iteration }}"
                                        class="aspect-square w-full rounded-lg border border-zinc-200 object-cover shadow-sm transition hover:shadow-md dark:border-zinc-700">
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if ($company->exhibitor->video_url)
                        <div>
                            <flux:text class="mb-3 text-sm text-zinc-500 dark:text-zinc-400">Video URL</flux:text>
                            <a href="{{ $company->exhibitor->video_url }}"
                                class="inline-flex items-center gap-2 text-blue-600 hover:underline dark:text-blue-400"
                                target="_blank" rel="noopener">
                                <flux:icon.play-circle variant="micro" />
                                {{ $company->exhibitor->video_url }}
                            </a>
                        </div>
                    @endif

                    @if ($company->exhibitor->social_media_links && count($company->exhibitor->social_media_links) > 0)
                        <div>
                            <flux:text class="mb-3 text-sm text-zinc-500 dark:text-zinc-400">Social Media</flux:text>
                            <div class="grid gap-3 sm:grid-cols-2">
                                @foreach ($company->exhibitor->social_media_links as $platform => $url)
                                    <a href="{{ $url }}"
                                        class="flex items-center gap-2 rounded-lg border border-zinc-200 p-3 transition hover:border-zinc-300 hover:bg-zinc-50 dark:border-zinc-700 dark:hover:border-zinc-600 dark:hover:bg-zinc-800"
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

            <!-- Exhibition Requirements -->
            <flux:card>
                <div class="border-b border-zinc-200 pb-4 dark:border-zinc-700">
                    <flux:heading size="lg">Exhibition Requirements</flux:heading>
                </div>

                <div class="grid gap-6 pt-6 md:grid-cols-2 lg:grid-cols-3">
                    @if ($company->exhibitor->facia_name)
                        <div>
                            <flux:text class="mb-1.5 text-sm text-zinc-500 dark:text-zinc-400">Facia Name</flux:text>
                            <flux:text class="font-medium">{{ $company->exhibitor->facia_name }}</flux:text>
                        </div>
                    @endif
                    @if ($company->exhibitor->momento_name)
                        <div>
                            <flux:text class="mb-1.5 text-sm text-zinc-500 dark:text-zinc-400">Momento Name</flux:text>
                            <flux:text class="font-medium">{{ $company->exhibitor->momento_name }}</flux:text>
                        </div>
                    @endif
                    @if ($company->exhibitor->extra_furniture_details)
                        <div class="lg:col-span-3">
                            <flux:text class="mb-1.5 text-sm text-zinc-500 dark:text-zinc-400">Extra Furniture
                                Details</flux:text>
                            <flux:text class="font-medium">{{ $company->exhibitor->extra_furniture_details }}
                            </flux:text>
                        </div>
                    @endif
                    @if ($company->exhibitor->exhibitor_passes_details)
                        <div class="lg:col-span-3">
                            <flux:text class="mb-1.5 text-sm text-zinc-500 dark:text-zinc-400">Exhibitor Passes
                                Details</flux:text>
                            <flux:text class="font-medium">{{ $company->exhibitor->exhibitor_passes_details }}
                            </flux:text>
                        </div>
                    @endif
                    @if ($company->exhibitor->car_pass_details)
                        <div class="lg:col-span-3">
                            <flux:text class="mb-1.5 text-sm text-zinc-500 dark:text-zinc-400">Car Pass Details
                            </flux:text>
                            <flux:text class="font-medium">{{ $company->exhibitor->car_pass_details }}</flux:text>
                        </div>
                    @endif
                    @if ($company->exhibitor->additional_details)
                        <div class="lg:col-span-3">
                            <flux:text class="mb-1.5 text-sm text-zinc-500 dark:text-zinc-400">Additional
                                Details</flux:text>
                            <flux:text class="font-medium">{{ $company->exhibitor->additional_details }}</flux:text>
                        </div>
                    @endif
                </div>
            </flux:card>

            <!-- Projects -->
            @if ($company->exhibitor->projects && $company->exhibitor->projects->count() > 0)
                <flux:card>
                    <div class="border-b border-zinc-200 pb-4 dark:border-zinc-700">
                        <flux:heading size="lg">Projects ({{ $company->exhibitor->projects->count() }})
                        </flux:heading>
                    </div>

                    <div class="space-y-6 pt-6">
                        @foreach ($company->exhibitor->projects as $project)
                            <div
                                class="rounded-lg border border-zinc-200 bg-zinc-50/50 p-6 dark:border-zinc-700 dark:bg-zinc-800/50">
                                <div class="mb-6 flex items-start justify-between gap-4">
                                    <div class="flex-1">
                                        <flux:heading size="md" class="mb-2">{{ $project->name }}
                                        </flux:heading>
                                        @if ($project->category)
                                            <flux:badge color="zinc">{{ $project->category }}</flux:badge>
                                        @endif
                                    </div>
                                    @if ($project->logo_path)
                                        <img src="{{ Storage::url($project->logo_path) }}"
                                            alt="{{ $project->name }} logo"
                                            class="h-20 w-20 rounded-lg border border-zinc-200 bg-white object-cover p-1 shadow-sm dark:border-zinc-700 dark:bg-zinc-800">
                                    @endif
                                </div>

                                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                                    @if ($project->area)
                                        <div>
                                            <flux:text class="mb-1.5 text-sm text-zinc-500 dark:text-zinc-400">Area
                                            </flux:text>
                                            <flux:text class="font-medium">{{ $project->area }}</flux:text>
                                        </div>
                                    @endif
                                    @if ($project->sq_ft)
                                        <div>
                                            <flux:text class="mb-1.5 text-sm text-zinc-500 dark:text-zinc-400">Size
                                            </flux:text>
                                            <flux:text class="font-medium">{{ $project->sq_ft }} sq. ft.</flux:text>
                                        </div>
                                    @endif
                                    @if ($project->budget_range)
                                        <div>
                                            <flux:text class="mb-1.5 text-sm text-zinc-500 dark:text-zinc-400">Budget
                                                Range</flux:text>
                                            <flux:text class="font-medium">{{ $project->budget_range }}</flux:text>
                                        </div>
                                    @endif
                                    @if ($project->handover_date)
                                        <div>
                                            <flux:text class="mb-1.5 text-sm text-zinc-500 dark:text-zinc-400">Handover
                                                Date</flux:text>
                                            <flux:text class="font-medium">{{ $project->handover_date }}</flux:text>
                                        </div>
                                    @endif
                                    @if ($project->status)
                                        <div>
                                            <flux:text class="mb-1.5 text-sm text-zinc-500 dark:text-zinc-400">Status
                                            </flux:text>
                                            <flux:text class="font-medium">{{ $project->status }}</flux:text>
                                        </div>
                                    @endif
                                    @if ($project->contact_person)
                                        <div>
                                            <flux:text class="mb-1.5 text-sm text-zinc-500 dark:text-zinc-400">Contact
                                                Person</flux:text>
                                            <flux:text class="font-medium">{{ $project->contact_person }}</flux:text>
                                        </div>
                                    @endif
                                    @if ($project->usp)
                                        <div class="lg:col-span-3">
                                            <flux:text class="mb-1.5 text-sm text-zinc-500 dark:text-zinc-400">Unique
                                                Selling
                                                Point</flux:text>
                                            <flux:text class="font-medium">{{ $project->usp }}</flux:text>
                                        </div>
                                    @endif
                                    @if ($project->video_url)
                                        <div class="lg:col-span-3">
                                            <flux:text class="mb-1.5 text-sm text-zinc-500 dark:text-zinc-400">Video
                                            </flux:text>
                                            <a href="{{ $project->video_url }}"
                                                class="inline-flex items-center gap-2 text-blue-600 hover:underline dark:text-blue-400"
                                                target="_blank" rel="noopener">
                                                <flux:icon.play-circle variant="micro" />
                                                {{ $project->video_url }}
                                            </a>
                                        </div>
                                    @endif
                                    @if ($project->pdf_path)
                                        <div class="lg:col-span-3">
                                            <flux:button variant="primary" size="sm" icon="document-text"
                                                href="{{ Storage::url($project->pdf_path) }}" target="_blank">
                                                Download Project PDF
                                            </flux:button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </flux:card>
            @endif
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
</div>
