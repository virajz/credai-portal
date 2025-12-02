<div class="mx-auto w-full">
    <div class="mb-8 flex items-start justify-between gap-6">
        <div class="flex-1">
            <flux:heading size="xl" class="mb-2">
                @if ($company->has_submitted)
                    Update Exhibitor Information
                @else
                    Exhibitor Registration
                @endif
            </flux:heading>
            <flux:subheading>
                @if ($company->has_submitted)
                    You can update your exhibitor information below
                @else
                    Complete your exhibitor information for the exhibition
                @endif
            </flux:subheading>
        </div>
        <div
            class="shrink-0 rounded-lg border border-yellow-300 bg-yellow-50 p-3 dark:border-yellow-700 dark:bg-yellow-900/20">
            <div class="flex items-start gap-2">
                <flux:icon.calendar class="mt-0.5 size-4 shrink-0 text-yellow-600 dark:text-yellow-500" />
                <div class="text-sm text-yellow-800 dark:text-yellow-200">
                    <strong>Important:</strong> Final submission deadline is 28th November, 2025. Please ensure all
                    information is accurate and complete.
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <flux:callout variant="success" class="mb-6">
            {{ session('success') }}
        </flux:callout>
    @endif

    <!-- Company Info Card (Locked) -->
    <flux:card class="mb-6 bg-zinc-50 dark:bg-zinc-900/50">
        <div class="space-y-4">
            <div class="flex items-center gap-2">
                <flux:icon.building-office-2 class="size-5 text-zinc-500" />
                <flux:heading size="base">Your Company Information</flux:heading>
                <flux:badge size="sm" color="zinc" icon="lock-closed">Locked</flux:badge>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div>
                    <flux:label class="text-xs text-zinc-500">Company Name</flux:label>
                    <flux:text class="font-semibold">{{ $company->company_name }}</flux:text>
                </div>
                <div>
                    <flux:label class="text-xs text-zinc-500">Contact Person</flux:label>
                    <flux:text class="font-semibold">{{ $company->main_person_name }}</flux:text>
                </div>
                <div>
                    <flux:label class="text-xs text-zinc-500">Mobile Number</flux:label>
                    <flux:text class="font-semibold">{{ $company->registered_number }}</flux:text>
                </div>
            </div>

            @if ($company->stall_number)
                <flux:separator />
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <flux:label class="text-xs text-zinc-500">Stall Type</flux:label>
                        <flux:text class="font-semibold">{{ $company->stall_type }}</flux:text>
                    </div>
                    <div>
                        <flux:label class="text-xs text-zinc-500">Stall Number</flux:label>
                        <flux:text class="font-semibold">{{ $company->stall_number }}</flux:text>
                    </div>
                    <div>
                        <flux:label class="text-xs text-zinc-500">Stall Size</flux:label>
                        <flux:text class="font-semibold">{{ $company->stall_size }}</flux:text>
                    </div>
                </div>
            @endif
        </div>
    </flux:card>

    <!-- Progress Indicator -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            @for ($i = 1; $i <= 3; $i++)
                <button type="button" wire:click="goToStep({{ $i }})"
                    class="{{ implode(
                        ' ',
                        array_filter([
                            'flex shrink-0 items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition',
                            $currentStep === $i ? 'bg-zinc-100 text-zinc-900 dark:bg-zinc-800 dark:text-white' : '',
                            in_array($i, $completedSteps) && $currentStep !== $i ? 'text-green-600 dark:text-green-500' : '',
                            !in_array($i, $completedSteps) && $currentStep !== $i ? 'text-zinc-400 dark:text-zinc-600' : '',
                        ]),
                    ) }}">
                    <span
                        class="{{ implode(
                            ' ',
                            array_filter([
                                'flex h-8 w-8 shrink-0 items-center justify-center rounded-full md:h-6 md:w-6',
                                $currentStep === $i ? 'bg-zinc-600 text-white dark:bg-zinc-400 dark:text-zinc-900' : '',
                                in_array($i, $completedSteps) && $currentStep !== $i ? 'bg-green-600 text-white dark:bg-green-500' : '',
                                !in_array($i, $completedSteps) && $currentStep !== $i
                                    ? 'bg-zinc-200 text-zinc-400 dark:bg-zinc-800 dark:text-zinc-600'
                                    : '',
                            ]),
                        ) }}">
                        @if (in_array($i, $completedSteps))
                            <flux:icon.check variant="outline" class="size-4 md:size-3.5" />
                        @else
                            <span class="text-xs font-semibold md:hidden">{{ $i }}</span>
                            <span class="hidden md:inline">
                                @if ($i === 1)
                                    <flux:icon.building-office-2 class="size-3.5" />
                                @elseif ($i === 2)
                                    <flux:icon.photo class="size-3.5" />
                                @elseif ($i === 3)
                                    <flux:icon.briefcase class="size-3.5" />
                                @endif
                            </span>
                        @endif
                    </span>
                    <span class="hidden whitespace-nowrap md:inline">
                        @if ($i === 1)
                            Company Details
                        @elseif ($i === 2)
                            Exhibition Display
                        @elseif ($i === 3)
                            Projects
                        @endif
                    </span>
                </button>

                @if ($i < 3)
                    <div
                        class="{{ implode(
                            ' ',
                            array_filter([
                                'h-0.5 flex-1',
                                in_array($i, $completedSteps) ? 'bg-green-600 dark:bg-green-500' : 'bg-zinc-300 dark:bg-zinc-700',
                            ]),
                        ) }}">
                    </div>
                @endif
            @endfor
        </div>
    </div>

    <!-- Current Step Header (Mobile Only) -->
    <div class="sticky top-0 z-30 mb-6 bg-white pb-3 dark:bg-zinc-950 md:hidden">
        <div
            class="rounded-lg border border-zinc-200 bg-gradient-to-r from-zinc-50 to-white p-4 dark:border-zinc-700 dark:from-zinc-900 dark:to-zinc-800">
            <div class="flex items-center gap-3">
                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-zinc-600 text-white dark:bg-zinc-400 dark:text-zinc-900">
                    @if ($currentStep === 1)
                        <flux:icon.building-office-2 class="size-5" />
                    @elseif ($currentStep === 2)
                        <flux:icon.photo class="size-5" />
                    @elseif ($currentStep === 3)
                        <flux:icon.briefcase class="size-5" />
                    @endif
                </div>
                <div>
                    <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Step {{ $currentStep }} of 3
                    </div>
                    <div class="text-base font-semibold text-zinc-900 dark:text-white">
                        @if ($currentStep === 1)
                            Company Details
                        @elseif ($currentStep === 2)
                            Exhibition Display
                        @elseif ($currentStep === 3)
                            Projects
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form wire:submit="submit" class="space-y-6">
        <!-- Step 1: Company Details -->
        @if ($currentStep === 1)
            <flux:card>
                <div class="space-y-6">
                    <flux:heading size="lg">Company Details</flux:heading>

                    <flux:textarea wire:model="office_address" label="Office Address"
                        placeholder="123 Main Street, Building Name, Area" rows="3" required />

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <flux:select wire:model="city" label="City" placeholder="Select city" variant="listbox"
                            searchable required>
                            @foreach ($cities as $cityOption)
                                <flux:select.option value="{{ $cityOption }}">{{ $cityOption }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>

                        <flux:input wire:model="gst_number" label="GST Number" placeholder="22AAAAA0000A1Z5"
                            badge="Optional" />
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <flux:input wire:model="pan_number" label="PAN Card" placeholder="AAAAA0000A"
                            badge="Optional" />

                        <flux:input wire:model="email" label="Email ID" type="email" placeholder="contact@example.com"
                            badge="Optional" />
                    </div>

                    <flux:input wire:model="website" label="Website URL" type="url"
                        placeholder="https://www.example.com" badge="Optional" />

                    <flux:separator />

                    <flux:heading size="lg">Branding & Media</flux:heading>

                    @include('components.exhibitor.media-upload-section')

                    <flux:textarea wire:model="additional_details" label="Additional Details"
                        placeholder="Any other information you'd like to share..." rows="3" badge="Optional" />
                </div>
            </flux:card>
        @endif

        <!-- Step 2: Exhibition Display -->
        @if ($currentStep === 2)
            <flux:card>
                <div class="space-y-6">
                    <flux:heading size="lg">Exhibition Display Details</flux:heading>

                    <flux:field>
                        <flux:label>Facia Board Name <span class="text-red-500">*</span></flux:label>
                        <flux:input wire:model="facia_name" placeholder="Enter the name for your booth fascia board"
                            required />
                        <flux:error name="facia_name" />
                        <flux:description>This will be displayed on your exhibition booth</flux:description>
                    </flux:field>

                    <flux:checkbox wire:model.live="use_brand_name_as_facia"
                        label="Use company name as facia name (uppercase)" />

                    <flux:separator />

                    <flux:heading size="base">Additional Requirements</flux:heading>

                    <flux:field>
                        <flux:label>Momento Name <flux:badge size="sm" color="zinc">Optional</flux:badge>
                        </flux:label>
                        <flux:input wire:model="momento_name" placeholder="Name to be engraved on the momento" />
                        <flux:error name="momento_name" />
                    </flux:field>

                    <flux:checkbox wire:model.live="use_brand_name_as_momento"
                        label="Use company name as momento name" />

                    <flux:textarea wire:model="extra_furniture_details" label="Extra Furniture Details"
                        placeholder="List any additional furniture requirements..." rows="3"
                        badge="Optional" />

                    <flux:textarea wire:model="exhibitor_passes_details" label="Exhibitor Passes Details"
                        placeholder="Specify exhibitor pass requirements..." rows="3" badge="Optional" />

                    <flux:textarea wire:model="car_pass_details" label="Car Pass Details"
                        placeholder="Specify car pass requirements..." rows="3" badge="Optional" />
                </div>
            </flux:card>
        @endif

        <!-- Step 3: Projects -->
        @if ($currentStep === 3)
            <div class="space-y-6">
                <!-- Header -->
                <flux:card>
                    <div class="flex items-center justify-between">
                        <div>
                            <flux:heading size="lg">Project Details</flux:heading>
                            <flux:subheading>Add your ongoing and upcoming projects</flux:subheading>
                        </div>
                        <flux:button type="button" size="sm" icon="plus" wire:click="addProject">
                            Add Project
                        </flux:button>
                    </div>
                </flux:card>

                @if (count($projects) === 0)
                    <flux:card>
                        <flux:callout variant="info">
                            <div class="flex items-start gap-3">
                                <flux:icon.information-circle class="mt-0.5 size-5 shrink-0" />
                                <div>
                                    <div class="font-semibold">No projects added yet</div>
                                    <div class="text-sm">Click "Add Project" to showcase your properties at the
                                        exhibition</div>
                                </div>
                            </div>
                        </flux:callout>
                    </flux:card>
                @else
                    <!-- Side by Side Layout -->
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
                        <!-- Projects List (Left Side) -->
                        <div class="lg:col-span-4">
                            <flux:card class="h-full">
                                <div class="space-y-2">
                                    <flux:heading size="base">Projects ({{ count($projects) }})</flux:heading>
                                    <div class="space-y-2">
                                        @foreach ($projects as $index => $project)
                                            <button type="button" wire:click="selectProject({{ $index }})"
                                                class="{{ $selectedProjectIndex === $index
                                                    ? 'border-blue-500 bg-blue-50 dark:border-blue-600 dark:bg-blue-900/20'
                                                    : 'border-zinc-200 bg-white hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:hover:bg-zinc-700' }} group flex w-full items-center justify-between rounded-lg border p-3 text-left transition">
                                                <div class="min-w-0 flex-1">
                                                    <div class="truncate text-sm font-medium text-zinc-900 dark:text-white"
                                                        title="{{ $project['name'] ?: 'Untitled Project' }}">
                                                        {{ $project['name'] ?: 'Untitled Project' }}
                                                    </div>
                                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                                        Project {{ $index + 1 }}
                                                        @if (!empty($project['area']))
                                                            • {{ $project['area'] }}
                                                        @endif
                                                    </div>
                                                </div>
                                                <flux:icon.chevron-right
                                                    class="size-4 shrink-0 text-zinc-400 group-hover:text-zinc-600 dark:text-zinc-500 dark:group-hover:text-zinc-300" />
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </flux:card>
                        </div>

                        <!-- Project Edit Form (Right Side) -->
                        <div class="lg:col-span-8">
                            @if ($selectedProjectIndex !== null && isset($projects[$selectedProjectIndex]))
                                @php $index = $selectedProjectIndex; @endphp
                                @php $project = $projects[$index]; @endphp
                                <flux:card wire:key="project-edit-{{ $selectedProjectIndex }}">
                                    <div class="space-y-4">
                                        <div class="flex items-center justify-between">
                                            <flux:heading size="base">Project {{ $index + 1 }}</flux:heading>
                                            <flux:button type="button" size="sm" variant="danger"
                                                icon="trash" wire:click="removeProject({{ $index }})">
                                                Remove
                                            </flux:button>
                                        </div>

                                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                            <flux:input wire:model="projects.{{ $index }}.name"
                                                label="Project Name" placeholder="Project name" required />

                                            <flux:select wire:model.live="projects.{{ $index }}.area"
                                                label="Area/Location" placeholder="Select area..." variant="listbox"
                                                searchable required>
                                                <flux:select.option value="Athwa - Vesu">Athwa - Vesu
                                                </flux:select.option>
                                                <flux:select.option value="Pal - Adajan - Rander">Pal - Adajan - Rander
                                                </flux:select.option>
                                                <flux:select.option value="Katargam">Katargam</flux:select.option>
                                                <flux:select.option value="Varachha">Varachha</flux:select.option>
                                                <flux:select.option value="Udhna - Sachin">Udhna - Sachin
                                                </flux:select.option>
                                                <flux:select.option value="Dindoli">Dindoli</flux:select.option>
                                                <flux:select.option value="Kamrej">Kamrej</flux:select.option>
                                                <flux:select.option value="Saroli">Saroli</flux:select.option>
                                                <flux:select.option value="Within City">Within City
                                                </flux:select.option>
                                                <flux:select.option value="Outer City">Outer City</flux:select.option>
                                                <flux:select.option value="Puna Kumbhaiya">Puna Kumbhaiya
                                                </flux:select.option>
                                                <flux:select.option value="Others">Others</flux:select.option>
                                            </flux:select>
                                        </div>

                                        @if (isset($projects[$index]['area']) && $projects[$index]['area'] === 'Others')
                                            <flux:input wire:model="projects.{{ $index }}.area_other"
                                                placeholder="Enter custom area/location" label="Custom Area"
                                                required />
                                        @endif

                                        <!-- Project Category -->
                                        <flux:radio.group wire:model.live="projects.{{ $index }}.category"
                                            label="Property Type" variant="cards" class="grid grid-cols-2 gap-3">
                                            <flux:radio value="Residential" label="Residential" />
                                            <flux:radio value="Commercial" label="Commercial" />
                                            <flux:radio value="Plotting" label="Plotting" />
                                            <flux:radio value="Weekend Home & Others" label="Weekend Home & Others" />
                                        </flux:radio.group>
                                        @error('projects.' . $index . '.category')
                                            <flux:text class="mt-2 text-sm text-red-600 dark:text-red-400">
                                                {{ $message }}
                                            </flux:text>
                                        @enderror

                                        <!-- Residential Units -->
                                        @if (isset($projects[$index]['category']) && $projects[$index]['category'] === 'Residential')
                                            <flux:card class="bg-zinc-50 dark:bg-zinc-900/50">
                                                <div class="space-y-4">
                                                    <div class="flex items-center justify-between">
                                                        <flux:heading size="base">Residential Units</flux:heading>
                                                        <flux:button type="button" size="sm" icon="plus"
                                                            wire:click="addResidentialUnit({{ $index }})">
                                                            Add Unit
                                                        </flux:button>
                                                    </div>

                                                    @error('projects.' . $index . '.units')
                                                        <flux:text class="text-sm text-red-600 dark:text-red-400">
                                                            {{ $message }}
                                                        </flux:text>
                                                    @enderror

                                                    @if (isset($projects[$index]['units']) && count($projects[$index]['units']) > 0)
                                                        <div class="space-y-3">
                                                            @foreach ($projects[$index]['units'] as $unitIndex => $unit)
                                                                <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800"
                                                                    wire:key="unit-{{ $index }}-{{ $unitIndex }}">
                                                                    <div
                                                                        class="mb-3 flex items-center justify-between">
                                                                        <flux:subheading>Unit {{ $unitIndex + 1 }}
                                                                        </flux:subheading>
                                                                        <flux:button type="button" size="sm"
                                                                            variant="danger" icon="trash"
                                                                            wire:click="removeResidentialUnit({{ $index }}, {{ $unitIndex }})">
                                                                            Remove
                                                                        </flux:button>
                                                                    </div>
                                                                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                                                        <flux:select
                                                                            wire:model="projects.{{ $index }}.units.{{ $unitIndex }}.bedrooms"
                                                                            label="Bedrooms" placeholder="Select..."
                                                                            variant="listbox">
                                                                            <flux:select.option value="1 BHK">1 BHK
                                                                            </flux:select.option>
                                                                            <flux:select.option value="2 BHK">2 BHK
                                                                            </flux:select.option>
                                                                            <flux:select.option value="3 BHK">3 BHK
                                                                            </flux:select.option>
                                                                            <flux:select.option value="4 BHK">4 BHK
                                                                            </flux:select.option>
                                                                            <flux:select.option value="5+ BHK">5+ BHK
                                                                            </flux:select.option>
                                                                        </flux:select>

                                                                        <flux:select
                                                                            wire:model="projects.{{ $index }}.units.{{ $unitIndex }}.budget"
                                                                            label="Budget" placeholder="Select..."
                                                                            variant="listbox">
                                                                            <flux:select.option value="Below 50L">Below
                                                                                50L</flux:select.option>
                                                                            <flux:select.option value="50L - 1Cr">50L -
                                                                                1Cr</flux:select.option>
                                                                            <flux:select.option value="1Cr - 2Cr">1Cr -
                                                                                2Cr</flux:select.option>
                                                                            <flux:select.option value="2Cr - 5Cr">2Cr -
                                                                                5Cr</flux:select.option>
                                                                            <flux:select.option value="Above 5Cr">Above
                                                                                5Cr</flux:select.option>
                                                                        </flux:select>

                                                                        <flux:input
                                                                            wire:model="projects.{{ $index }}.units.{{ $unitIndex }}.area"
                                                                            label="Area (sq. ft.)" type="number"
                                                                            placeholder="0" />
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <flux:text class="text-sm text-zinc-500">No units added yet.
                                                            Click "Add
                                                            Unit" to add residential units.</flux:text>
                                                    @endif
                                                </div>
                                            </flux:card>
                                        @endif

                                        <!-- Commercial Units -->
                                        @if (isset($projects[$index]['category']) && $projects[$index]['category'] === 'Commercial')
                                            <flux:card class="bg-zinc-50 dark:bg-zinc-900/50">
                                                <div class="space-y-4">
                                                    <div class="flex items-center justify-between">
                                                        <flux:heading size="base">Commercial Units</flux:heading>
                                                        <flux:button type="button" size="sm" icon="plus"
                                                            wire:click="addCommercialUnit({{ $index }})">
                                                            Add Unit
                                                        </flux:button>
                                                    </div>

                                                    @error('projects.' . $index . '.units')
                                                        <flux:text class="text-sm text-red-600 dark:text-red-400">
                                                            {{ $message }}
                                                        </flux:text>
                                                    @enderror

                                                    @if (isset($projects[$index]['units']) && count($projects[$index]['units']) > 0)
                                                        <div class="space-y-3">
                                                            @foreach ($projects[$index]['units'] as $unitIndex => $unit)
                                                                <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800"
                                                                    wire:key="unit-{{ $index }}-{{ $unitIndex }}">
                                                                    <div
                                                                        class="mb-3 flex items-center justify-between">
                                                                        <flux:subheading>Unit {{ $unitIndex + 1 }}
                                                                        </flux:subheading>
                                                                        <flux:button type="button" size="sm"
                                                                            variant="danger" icon="trash"
                                                                            wire:click="removeCommercialUnit({{ $index }}, {{ $unitIndex }})">
                                                                            Remove
                                                                        </flux:button>
                                                                    </div>
                                                                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                                                        <flux:input
                                                                            wire:model="projects.{{ $index }}.units.{{ $unitIndex }}.area"
                                                                            label="Area (sq. ft.)" type="number"
                                                                            placeholder="0" />

                                                                        <flux:select
                                                                            wire:model="projects.{{ $index }}.units.{{ $unitIndex }}.budget"
                                                                            label="Budget" placeholder="Select..."
                                                                            variant="listbox">
                                                                            <flux:select.option value="Below 50L">Below
                                                                                50L</flux:select.option>
                                                                            <flux:select.option value="50L - 1Cr">50L -
                                                                                1Cr</flux:select.option>
                                                                            <flux:select.option value="1Cr - 2Cr">1Cr -
                                                                                2Cr</flux:select.option>
                                                                            <flux:select.option value="2Cr - 5Cr">2Cr -
                                                                                5Cr</flux:select.option>
                                                                            <flux:select.option value="Above 5Cr">Above
                                                                                5Cr</flux:select.option>
                                                                        </flux:select>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <flux:text class="text-sm text-zinc-500">No units added yet.
                                                            Click "Add
                                                            Unit" to add commercial units.</flux:text>
                                                    @endif
                                                </div>
                                            </flux:card>
                                        @endif

                                        <!-- Plotting - Single Area Field -->
                                        @if (isset($projects[$index]['category']) && $projects[$index]['category'] === 'Plotting')
                                            <flux:input wire:model="projects.{{ $index }}.sq_ft"
                                                label="Area (sq. ft.)" type="number" placeholder="0" required />
                                        @endif

                                        <!-- Budget Range (Hidden for Residential and Commercial as they use units) -->
                                        @if (!isset($projects[$index]['category']) || !in_array($projects[$index]['category'], ['Residential', 'Commercial']))
                                            <flux:radio.group wire:model="projects.{{ $index }}.budget_range"
                                                label="Budget Range" variant="cards" class="max-sm:flex-col">
                                                <flux:radio value="Below 50L" label="Below 50L" />
                                                <flux:radio value="50L - 1Cr" label="50L - 1Cr" />
                                                <flux:radio value="1Cr - 2Cr" label="1Cr - 2Cr" />
                                                <flux:radio value="2Cr - 5Cr" label="2Cr - 5Cr" />
                                                <flux:radio value="Above 5Cr" label="Above 5Cr" />
                                            </flux:radio.group>
                                            @error('projects.' . $index . '.budget_range')
                                                <flux:text class="mt-2 text-sm text-red-600 dark:text-red-400">
                                                    {{ $message }}
                                                </flux:text>
                                            @enderror
                                        @endif

                                        <!-- Expected Handover Date -->
                                        <flux:radio.group wire:model="projects.{{ $index }}.handover_date"
                                            label="Expected Handover Date" variant="cards" class="max-sm:flex-col">
                                            <flux:radio value="Within 3 months" label="Within 3 months" />
                                            <flux:radio value="Within 6 months" label="Within 6 months" />
                                            <flux:radio value="Within a year" label="Within a year" />
                                        </flux:radio.group>
                                        @error('projects.' . $index . '.handover_date')
                                            <flux:text class="mt-2 text-sm text-red-600 dark:text-red-400">
                                                {{ $message }}
                                            </flux:text>
                                        @enderror

                                        <!-- Project Status -->
                                        <flux:radio.group wire:model="projects.{{ $index }}.status"
                                            label="Project Status" variant="cards" class="max-sm:flex-col">
                                            <flux:radio value="upcoming" label="Upcoming" />
                                            <flux:radio value="ongoing" label="Ongoing" />
                                            <flux:radio value="completed" label="Completed" />
                                            <flux:radio value="ready_to_move" label="Ready to Move" />
                                        </flux:radio.group>
                                        @error('projects.' . $index . '.status')
                                            <flux:text class="mt-2 text-sm text-red-600 dark:text-red-400">
                                                {{ $message }}
                                            </flux:text>
                                        @enderror

                                        <flux:textarea wire:model="projects.{{ $index }}.usp"
                                            label="Unique Selling Points"
                                            placeholder="What makes this project special?" rows="3"
                                            badge="Optional" />

                                        <flux:input wire:model="projects.{{ $index }}.contact_person"
                                            label="Contact Person" placeholder="Project contact name"
                                            badge="Optional" />

                                        <!-- Project Media -->
                                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                            <!-- Project PDF -->
                                            <div x-data="{
                                                uploading: false,
                                                isDragging: false,
                                                handleFile(event) {
                                                    const file = event.target.files[0];
                                                    if (file) {
                                                        this.uploading = true;
                                                        @this.upload('projects.{{ $index }}.pdf', file, () => {
                                                            this.uploading = false;
                                                        }, () => {
                                                            this.uploading = false;
                                                        })
                                                    }
                                                },
                                                handleDrop(event) {
                                                    event.preventDefault();
                                                    this.isDragging = false;
                                                    const file = event.dataTransfer.files[0];
                                                    if (file && file.type === 'application/pdf') {
                                                        this.uploading = true;
                                                        @this.upload('projects.{{ $index }}.pdf', file, () => {
                                                            this.uploading = false;
                                                        }, () => {
                                                            this.uploading = false;
                                                        })
                                                    }
                                                }
                                            }">
                                                <div class="space-y-2">
                                                    <flux:label badge="Optional">
                                                        Project Brochure/PDF
                                                    </flux:label>

                                                    <div class="relative">
                                                        <input type="file" @change="handleFile($event)"
                                                            accept="application/pdf" class="hidden"
                                                            id="project-{{ $index }}-pdf-upload"
                                                            :disabled="uploading ||
                                                                {{ isset($project['pdf']) ? 'true' : 'false' }}" />
                                                        <label for="project-{{ $index }}-pdf-upload"
                                                            @dragover.prevent="isDragging = true"
                                                            @dragleave.prevent="isDragging = false"
                                                            @drop.prevent="handleDrop($event)"
                                                            class="{{ implode(' ', [
                                                                'flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed',
                                                                'border-zinc-300 bg-zinc-50 px-4 py-6 transition hover:border-zinc-400',
                                                                'dark:border-zinc-700 dark:bg-zinc-900 dark:hover:border-zinc-600',
                                                            ]) }}"
                                                            :class="{
                                                                'cursor-not-allowed opacity-60': uploading ||
                                                                    {{ isset($project['pdf']) ? 'true' : 'false' }},
                                                                'border-blue-400 bg-blue-50 dark:border-blue-500 dark:bg-blue-900/20': isDragging
                                                            }">
                                                            <flux:icon.document
                                                                class="mb-2 size-8 text-zinc-400 dark:text-zinc-600" />
                                                            <span
                                                                class="text-sm font-medium text-zinc-700 dark:text-zinc-300"
                                                                x-show="!uploading">
                                                                Drop PDF or browse
                                                            </span>
                                                            <span
                                                                class="text-sm font-medium text-zinc-700 dark:text-zinc-300"
                                                                x-show="uploading" x-cloak>
                                                                Uploading...
                                                            </span>
                                                        </label>
                                                    </div>

                                                    <div x-show="uploading" x-cloak
                                                        class="rounded-lg border border-blue-200 bg-blue-50 p-2 dark:border-blue-800 dark:bg-blue-900/20">
                                                        <div
                                                            class="text-xs font-medium text-blue-700 dark:text-blue-300">
                                                            Uploading PDF...</div>
                                                        <div
                                                            class="mt-1 h-1.5 w-full overflow-hidden rounded-full bg-blue-200 dark:bg-blue-900">
                                                            <div
                                                                class="h-full w-full animate-pulse bg-blue-600 dark:bg-blue-400">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    @if (isset($project['pdf']))
                                                        <div class="mt-2">
                                                            <flux:file-item
                                                                :heading="$project['pdf']->getClientOriginalName()"
                                                                :size="$project['pdf']->getSize()">
                                                                <x-slot name="actions">
                                                                    <flux:file-item.remove
                                                                        wire:click="removeProjectFile({{ $index }}, 'pdf')"
                                                                        aria-label="Remove PDF" />
                                                                </x-slot>
                                                            </flux:file-item>
                                                        </div>
                                                    @elseif (isset($project['pdf_path']) && $project['pdf_path'])
                                                        <div class="mt-2">
                                                            <div
                                                                class="flex items-center gap-3 rounded-lg border border-zinc-200 bg-white p-3 dark:border-zinc-700 dark:bg-zinc-800">
                                                                <div
                                                                    class="rounded-md bg-red-100 p-2 shrink-0 dark:bg-red-900/30">
                                                                    <flux:icon.document
                                                                        class="size-5 text-red-600 dark:text-red-400" />
                                                                </div>
                                                                <div class="min-w-0 flex-1">
                                                                    <div class="truncate text-sm font-medium text-zinc-900 dark:text-white"
                                                                        title="{{ preg_replace('/^\d+_/', '', basename($project['pdf_path'])) }}">
                                                                        {{ preg_replace('/^\d+_/', '', basename($project['pdf_path'])) }}
                                                                    </div>
                                                                    <div
                                                                        class="text-xs text-zinc-500 dark:text-zinc-400">
                                                                        Project
                                                                        Brochure</div>
                                                                </div>
                                                                <div class="flex gap-1 shrink-0">
                                                                    <flux:button size="sm" variant="ghost"
                                                                        href="{{ Storage::url($project['pdf_path']) }}"
                                                                        download="{{ preg_replace('/^\d+_/', '', basename($project['pdf_path'])) }}"
                                                                        square aria-label="Download PDF">
                                                                        <flux:icon.arrow-down-tray class="size-4" />
                                                                    </flux:button>
                                                                    <flux:button size="sm" variant="ghost"
                                                                        wire:click="removeProjectFile({{ $index }}, 'pdf')"
                                                                        square aria-label="Replace PDF">
                                                                        <flux:icon.trash class="size-4" />
                                                                    </flux:button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @error('projects.' . $index . '.pdf')
                                                        <flux:text class="text-sm text-red-600 dark:text-red-400">
                                                            {{ $message }}</flux:text>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!-- Project Logo -->
                                            <div x-data="{
                                                uploading: false,
                                                isDragging: false,
                                                handleFile(event) {
                                                    const file = event.target.files[0];
                                                    if (file) {
                                                        this.uploading = true;
                                                        @this.upload('projects.{{ $index }}.logo', file, () => {
                                                            this.uploading = false;
                                                        }, () => {
                                                            this.uploading = false;
                                                        })
                                                    }
                                                },
                                                handleDrop(event) {
                                                    event.preventDefault();
                                                    this.isDragging = false;
                                                    const file = event.dataTransfer.files[0];
                                                    if (file && file.type.match(/^image\/(png|jpeg|jpg)$/)) {
                                                        this.uploading = true;
                                                        @this.upload('projects.{{ $index }}.logo', file, () => {
                                                            this.uploading = false;
                                                        }, () => {
                                                            this.uploading = false;
                                                        })
                                                    }
                                                }
                                            }">
                                                <div class="space-y-2">
                                                    <flux:label badge="Optional">
                                                        Project Logo
                                                    </flux:label>

                                                    <div class="relative">
                                                        <input type="file" @change="handleFile($event)"
                                                            accept="image/png,image/jpeg,image/jpg" class="hidden"
                                                            id="project-{{ $index }}-logo-upload"
                                                            :disabled="uploading ||
                                                                {{ isset($project['logo']) ? 'true' : 'false' }}" />
                                                        <label for="project-{{ $index }}-logo-upload"
                                                            @dragover.prevent="isDragging = true"
                                                            @dragleave.prevent="isDragging = false"
                                                            @drop.prevent="handleDrop($event)"
                                                            class="{{ implode(' ', [
                                                                'flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed',
                                                                'border-zinc-300 bg-zinc-50 px-4 py-6 transition hover:border-zinc-400',
                                                                'dark:border-zinc-700 dark:bg-zinc-900 dark:hover:border-zinc-600',
                                                            ]) }}"
                                                            :class="{
                                                                'cursor-not-allowed opacity-60': uploading ||
                                                                    {{ isset($project['logo']) ? 'true' : 'false' }},
                                                                'border-blue-400 bg-blue-50 dark:border-blue-500 dark:bg-blue-900/20': isDragging
                                                            }">
                                                            <flux:icon.photo
                                                                class="mb-2 size-8 text-zinc-400 dark:text-zinc-600" />
                                                            <span
                                                                class="text-sm font-medium text-zinc-700 dark:text-zinc-300"
                                                                x-show="!uploading">
                                                                Drop image or browse
                                                            </span>
                                                            <span
                                                                class="text-sm font-medium text-zinc-700 dark:text-zinc-300"
                                                                x-show="uploading" x-cloak>
                                                                Uploading...
                                                            </span>
                                                        </label>
                                                    </div>

                                                    <div x-show="uploading" x-cloak
                                                        class="rounded-lg border border-blue-200 bg-blue-50 p-2 dark:border-blue-800 dark:bg-blue-900/20">
                                                        <div
                                                            class="text-xs font-medium text-blue-700 dark:text-blue-300">
                                                            Uploading logo...</div>
                                                        <div
                                                            class="mt-1 h-1.5 w-full overflow-hidden rounded-full bg-blue-200 dark:bg-blue-900">
                                                            <div
                                                                class="h-full w-full animate-pulse bg-blue-600 dark:bg-blue-400">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    @if (isset($project['logo']))
                                                        <div class="mt-2">
                                                            <flux:file-item
                                                                :heading="$project['logo']->getClientOriginalName()"
                                                                :image="$project['logo']->temporaryUrl()"
                                                                :size="$project['logo']->getSize()">
                                                                <x-slot name="actions">
                                                                    <flux:file-item.remove
                                                                        wire:click="removeProjectFile({{ $index }}, 'logo')"
                                                                        aria-label="Remove logo" />
                                                                </x-slot>
                                                            </flux:file-item>
                                                        </div>
                                                    @elseif (isset($project['logo_path']) && $project['logo_path'])
                                                        <div class="mt-2">
                                                            <div
                                                                class="flex items-center gap-3 rounded-lg border border-zinc-200 bg-white p-3 dark:border-zinc-700 dark:bg-zinc-800">
                                                                <div
                                                                    class="rounded-md bg-blue-100 p-2 shrink-0 dark:bg-blue-900/30">
                                                                    <flux:icon.photo
                                                                        class="size-5 text-blue-600 dark:text-blue-400" />
                                                                </div>
                                                                <div class="min-w-0 flex-1">
                                                                    <div class="truncate text-sm font-medium text-zinc-900 dark:text-white"
                                                                        title="{{ preg_replace('/^\d+_/', '', basename($project['logo_path'])) }}">
                                                                        {{ preg_replace('/^\d+_/', '', basename($project['logo_path'])) }}
                                                                    </div>
                                                                    <div
                                                                        class="text-xs text-zinc-500 dark:text-zinc-400">
                                                                        Project Logo
                                                                    </div>
                                                                </div>
                                                                <div class="flex gap-1 shrink-0">
                                                                    <flux:button size="sm" variant="ghost"
                                                                        href="{{ Storage::url($project['logo_path']) }}"
                                                                        download="{{ preg_replace('/^\d+_/', '', basename($project['logo_path'])) }}"
                                                                        square aria-label="Download logo">
                                                                        <flux:icon.arrow-down-tray class="size-4" />
                                                                    </flux:button>
                                                                    <flux:button size="sm" variant="ghost"
                                                                        wire:click="removeProjectFile({{ $index }}, 'logo')"
                                                                        square aria-label="Replace logo">
                                                                        <flux:icon.trash class="size-4" />
                                                                    </flux:button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @error('projects.' . $index . '.logo')
                                                        <flux:text class="text-sm text-red-600 dark:text-red-400">
                                                            {{ $message }}</flux:text>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <flux:input wire:model="projects.{{ $index }}.video_url"
                                            label="Video URL" type="url" placeholder="https://youtube.com/..."
                                            badge="Optional" />
                                    </div>
                                </flux:card>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Navigation Buttons - Sticky Footer -->
        <div
            class="sticky bottom-0 z-40 -mx-6 mt-6 border-t border-zinc-200 bg-white px-6 py-4 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)] dark:border-zinc-700 dark:bg-zinc-900 dark:shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.3)] md:mx-0 md:rounded-lg md:border md:shadow-lg">
            <div class="flex justify-between gap-4">
                <div>
                    @if ($currentStep > 1)
                        <flux:button type="button" variant="ghost" icon="arrow-left" wire:click="previousStep">
                            Previous
                        </flux:button>
                    @endif
                </div>

                <div>
                    @if ($currentStep < 3)
                        <flux:button type="button" variant="primary" icon:trailing="arrow-right"
                            wire:click="nextStep" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="nextStep">Next</span>
                            <span wire:loading wire:target="nextStep">Validating...</span>
                        </flux:button>
                    @else
                        <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="submit">
                                @if ($company->has_submitted)
                                    Update Information
                                @else
                                    Submit Registration
                                @endif
                            </span>
                            <span wire:loading wire:target="submit">
                                @if ($company->has_submitted)
                                    Updating...
                                @else
                                    Submitting...
                                @endif
                            </span>
                        </flux:button>
                    @endif
                </div>
            </div>
        </div>
    </form>

    <!-- Auto-save Indicator -->
    <div class="fixed bottom-6 left-6 z-40">
        <div wire:loading.delay.longest
            class="flex items-center gap-2 rounded-lg border border-zinc-200 bg-white px-4 py-2 shadow-lg dark:border-zinc-700 dark:bg-zinc-900">
            <flux:icon.arrow-path class="size-4 animate-spin text-zinc-500" />
            <flux:text class="text-sm">Saving...</flux:text>
        </div>
    </div>

    <!-- Thank You Modal -->
    <flux:modal name="thank-you" class="w-full max-w-lg">
        <div class="space-y-6">
            <!-- Success Icon -->
            <div class="flex justify-center">
                <div class="flex size-16 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/20">
                    <flux:icon.check-circle class="size-10 text-green-600 dark:text-green-500" variant="solid" />
                </div>
            </div>

            <!-- Heading -->
            <div class="text-center">
                <flux:heading size="xl" class="mb-2">Thank You!</flux:heading>
                <flux:subheading>Your exhibitor information has been saved successfully.</flux:subheading>
            </div>

            <!-- Important Notice -->
            <flux:card class="border-2 border-yellow-300 bg-yellow-50 dark:border-yellow-700 dark:bg-yellow-900/20">
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <flux:icon.calendar class="mt-0.5 size-5 shrink-0 text-yellow-600 dark:text-yellow-500" />
                        <div>
                            <flux:heading size="base" class="mb-1 text-yellow-900 dark:text-yellow-100">
                                Important Reminder
                            </flux:heading>
                            <flux:text class="text-sm text-yellow-800 dark:text-yellow-200">
                                Final submission deadline is <strong>28th November, 2025</strong>. You can update your
                                information anytime before this date using the same registration link.
                            </flux:text>
                        </div>
                    </div>
                </div>
            </flux:card>

            <!-- Additional Info -->
            <div class="rounded-lg bg-zinc-50 p-4 dark:bg-zinc-800">
                <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">
                    We've saved your information. You can return to this page anytime to make updates or review your
                    submission.
                </flux:text>
            </div>

            <!-- Actions -->
            <div class="flex gap-3">
                <flux:button wire:click="closeThankYouModal" variant="primary" class="flex-1">
                    Got it, thanks!
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
