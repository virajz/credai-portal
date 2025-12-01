<div class="mx-auto w-full max-w-6xl space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ $exhibitor->company->company_name }}</flux:heading>
            <flux:subheading>Exhibitor Details</flux:subheading>
        </div>
        <div class="flex items-center gap-3">
            <flux:button href="{{ route('exhibitors.analytics', $exhibitor) }}" variant="primary" icon="chart-bar">
                View Analytics
            </flux:button>
            <flux:button href="{{ route('exhibitors.index') }}" variant="ghost" icon="arrow-left">
                Back to List
            </flux:button>
        </div>
    </div>

    <!-- Company Information -->
    <flux:card>
        <div class="space-y-6">
            <flux:heading size="lg">Company Information</flux:heading>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <div>
                    <flux:label class="text-xs text-zinc-500">Company Name</flux:label>
                    <flux:text class="font-semibold">{{ $exhibitor->company->company_name }}</flux:text>
                </div>
                <div>
                    <flux:label class="text-xs text-zinc-500">Contact Person</flux:label>
                    <flux:text class="font-semibold">{{ $exhibitor->company->main_person_name }}</flux:text>
                </div>
                <div>
                    <flux:label class="text-xs text-zinc-500">Mobile Number</flux:label>
                    <flux:text class="font-semibold">{{ $exhibitor->company->registered_number }}</flux:text>
                </div>
            </div>

            <flux:separator />

            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <div>
                    <flux:label class="text-xs text-zinc-500">Stall Type</flux:label>
                    <flux:text class="font-semibold">{{ $exhibitor->company->stall_type ?? 'N/A' }}</flux:text>
                </div>
                <div>
                    <flux:label class="text-xs text-zinc-500">Stall Number</flux:label>
                    <flux:text class="font-semibold">{{ $exhibitor->company->stall_number ?? 'N/A' }}</flux:text>
                </div>
                <div>
                    <flux:label class="text-xs text-zinc-500">Stall Size</flux:label>
                    <flux:text class="font-semibold">{{ $exhibitor->company->stall_size ?? 'N/A' }}</flux:text>
                </div>
            </div>

            <flux:separator />

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <flux:label class="text-xs text-zinc-500">Office Address</flux:label>
                    <flux:text>{{ $exhibitor->office_address }}</flux:text>
                </div>
                <div>
                    <flux:label class="text-xs text-zinc-500">City</flux:label>
                    <flux:text>{{ $exhibitor->city }}</flux:text>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <flux:label class="text-xs text-zinc-500">GST Number</flux:label>
                    <flux:text>{{ $exhibitor->gst_number ?? 'N/A' }}</flux:text>
                </div>
                <div>
                    <flux:label class="text-xs text-zinc-500">PAN Number</flux:label>
                    <flux:text>{{ $exhibitor->pan_number ?? 'N/A' }}</flux:text>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <flux:label class="text-xs text-zinc-500">Email</flux:label>
                    <flux:text>{{ $exhibitor->email ?? 'N/A' }}</flux:text>
                </div>
                <div>
                    <flux:label class="text-xs text-zinc-500">Website</flux:label>
                    @if ($exhibitor->website)
                        <a href="{{ $exhibitor->website }}" target="_blank"
                            class="text-sm text-blue-600 hover:underline dark:text-blue-400">
                            {{ $exhibitor->website }}
                        </a>
                    @else
                        <flux:text>N/A</flux:text>
                    @endif
                </div>
            </div>
        </div>
    </flux:card>

    <!-- Branding & Media -->
    <flux:card>
        <div class="space-y-6">
            <flux:heading size="lg">Branding & Media</flux:heading>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <flux:label class="text-xs text-zinc-500">Logo</flux:label>
                    @if ($exhibitor->logo_path)
                        <div class="mt-2">
                            <a href="{{ Storage::url($exhibitor->logo_path) }}" target="_blank"
                                class="inline-flex items-center gap-2 text-sm text-blue-600 hover:underline dark:text-blue-400">
                                <flux:icon.document class="size-4" />
                                View Logo
                            </a>
                        </div>
                    @else
                        <flux:text>N/A</flux:text>
                    @endif
                </div>

                <div>
                    <flux:label class="text-xs text-zinc-500">Brochure</flux:label>
                    @if ($exhibitor->brochure_path)
                        <div class="mt-2">
                            <a href="{{ Storage::url($exhibitor->brochure_path) }}" target="_blank"
                                class="inline-flex items-center gap-2 text-sm text-blue-600 hover:underline dark:text-blue-400">
                                <flux:icon.document class="size-4" />
                                Download Brochure
                            </a>
                        </div>
                    @else
                        <flux:text>N/A</flux:text>
                    @endif
                </div>
            </div>

            <div>
                <flux:label class="text-xs text-zinc-500">Video URL</flux:label>
                @if ($exhibitor->video_url)
                    <a href="{{ $exhibitor->video_url }}" target="_blank"
                        class="text-sm text-blue-600 hover:underline dark:text-blue-400">
                        {{ $exhibitor->video_url }}
                    </a>
                @else
                    <flux:text>N/A</flux:text>
                @endif
            </div>

            @if ($exhibitor->social_media_links)
                <div>
                    <flux:label class="text-xs text-zinc-500">Social Media Links</flux:label>
                    <div class="mt-2 flex flex-wrap gap-3">
                        @foreach ($exhibitor->social_media_links as $platform => $url)
                            @if ($url)
                                <a href="{{ $url }}" target="_blank"
                                    class="inline-flex items-center gap-2 rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:hover:bg-zinc-700">
                                    <x-dynamic-component :component="'bi-' . $platform" class="size-4" />
                                    {{ ucfirst($platform) }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($exhibitor->additional_details)
                <div>
                    <flux:label class="text-xs text-zinc-500">Additional Details</flux:label>
                    <flux:text class="mt-1">{{ $exhibitor->additional_details }}</flux:text>
                </div>
            @endif
        </div>
    </flux:card>

    <!-- Exhibition Display -->
    <flux:card>
        <div class="space-y-6">
            <flux:heading size="lg">Exhibition Display</flux:heading>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <flux:label class="text-xs text-zinc-500">Facia Board Name</flux:label>
                    <flux:text class="font-semibold">{{ $exhibitor->facia_name }}</flux:text>
                </div>

                <div>
                    <flux:label class="text-xs text-zinc-500">Momento Name</flux:label>
                    <flux:text>{{ $exhibitor->momento_name ?? 'N/A' }}</flux:text>
                </div>
            </div>

            @if ($exhibitor->extra_furniture_details)
                <div>
                    <flux:label class="text-xs text-zinc-500">Extra Furniture Details</flux:label>
                    <flux:text class="mt-1">{{ $exhibitor->extra_furniture_details }}</flux:text>
                </div>
            @endif

            @if ($exhibitor->exhibitor_passes_details)
                <div>
                    <flux:label class="text-xs text-zinc-500">Exhibitor Passes Details</flux:label>
                    <flux:text class="mt-1">{{ $exhibitor->exhibitor_passes_details }}</flux:text>
                </div>
            @endif

            @if ($exhibitor->car_pass_details)
                <div>
                    <flux:label class="text-xs text-zinc-500">Car Pass Details</flux:label>
                    <flux:text class="mt-1">{{ $exhibitor->car_pass_details }}</flux:text>
                </div>
            @endif
        </div>
    </flux:card>

    <!-- Projects -->
    <flux:card>
        <div class="space-y-6">
            <flux:heading size="lg">Projects ({{ $exhibitor->projects->count() }})</flux:heading>

            @if ($exhibitor->projects->isEmpty())
                <flux:callout variant="info">
                    No projects added for this exhibitor.
                </flux:callout>
            @else
                <div class="space-y-4">
                    @foreach ($exhibitor->projects as $project)
                        <flux:card class="border-2 border-zinc-200 dark:border-zinc-700">
                            <div class="space-y-4">
                                <flux:heading size="base">{{ $project->name }}</flux:heading>

                                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                    @if ($project->area)
                                        <div>
                                            <flux:label class="text-xs text-zinc-500">Area/Location</flux:label>
                                            <flux:text>{{ $project->area }}</flux:text>
                                        </div>
                                    @endif

                                    @if ($project->category)
                                        <div>
                                            <flux:label class="text-xs text-zinc-500">Category</flux:label>
                                            <flux:text>{{ $project->category }}</flux:text>
                                        </div>
                                    @endif

                                    @if ($project->sq_ft)
                                        <div>
                                            <flux:label class="text-xs text-zinc-500">Square Footage</flux:label>
                                            <flux:text>{{ $project->sq_ft }} sq ft</flux:text>
                                        </div>
                                    @endif
                                </div>

                                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                    @if ($project->budget_range)
                                        <div>
                                            <flux:label class="text-xs text-zinc-500">Budget Range</flux:label>
                                            <flux:text>{{ $project->budget_range }}</flux:text>
                                        </div>
                                    @endif

                                    @if ($project->handover_date)
                                        <div>
                                            <flux:label class="text-xs text-zinc-500">Handover Date</flux:label>
                                            <flux:text>
                                                {{ \Carbon\Carbon::parse($project->handover_date)->format('M d, Y') }}
                                            </flux:text>
                                        </div>
                                    @endif

                                    @if ($project->status)
                                        <div>
                                            <flux:label class="text-xs text-zinc-500">Status</flux:label>
                                            <flux:text>{{ $project->status }}</flux:text>
                                        </div>
                                    @endif
                                </div>

                                @if ($project->usp)
                                    <div>
                                        <flux:label class="text-xs text-zinc-500">Unique Selling Points</flux:label>
                                        <flux:text class="mt-1">{{ $project->usp }}</flux:text>
                                    </div>
                                @endif

                                @if ($project->contact_person)
                                    <div>
                                        <flux:label class="text-xs text-zinc-500">Contact Person</flux:label>
                                        <flux:text>{{ $project->contact_person }}</flux:text>
                                    </div>
                                @endif

                                @if ($project->video_url)
                                    <div>
                                        <flux:label class="text-xs text-zinc-500">Video URL</flux:label>
                                        <a href="{{ $project->video_url }}" target="_blank"
                                            class="text-sm text-blue-600 hover:underline dark:text-blue-400">
                                            {{ $project->video_url }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </flux:card>
                    @endforeach
                </div>
            @endif
        </div>
    </flux:card>

    <!-- Submission Info -->
    <flux:card>
        <div class="space-y-4">
            <flux:heading size="base">Submission Information</flux:heading>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <flux:label class="text-xs text-zinc-500">Submitted At</flux:label>
                    <flux:text>{{ $exhibitor->created_at->format('M d, Y h:i A') }}</flux:text>
                </div>
                <div>
                    <flux:label class="text-xs text-zinc-500">Last Updated</flux:label>
                    <flux:text>{{ $exhibitor->updated_at->format('M d, Y h:i A') }}</flux:text>
                </div>
            </div>
        </div>
    </flux:card>
</div>
