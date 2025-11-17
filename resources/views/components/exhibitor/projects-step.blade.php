@if ($currentStep === 3)
    <flux:card class="space-y-6">
        <div>
            <flux:heading size="lg">Project Details</flux:heading>
        </div>

        @if (count($projects) > 0)
            @foreach ($projects as $index => $project)
                <div class="rounded-lg border border-zinc-200 p-6 dark:border-zinc-700">
                    <div class="mb-4 flex items-start justify-between">
                        <flux:heading size="base">Project {{ $index + 1 }}</flux:heading>
                        <flux:button size="sm" variant="ghost" color="red"
                            wire:click="removeProject({{ $index }})" icon="x-mark">
                            Remove
                        </flux:button>
                    </div>

                    <flux:separator class="mb-6" />

                    <div class="space-y-6">
                        <!-- Basic Project Info -->
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <flux:input wire:model="projects.{{ $index }}.name" label="Project Name"
                                type="text" placeholder="e.g., Sunset Residency" required />

                            <flux:select wire:model.live="projects.{{ $index }}.area" label="Area/Location"
                                placeholder="Select area..." variant="listbox" searchable required>
                                <flux:select.option value="Athwa - Vesu">Athwa - Vesu</flux:select.option>
                                <flux:select.option value="Pal - Adajan - Rander">Pal - Adajan - Rander
                                </flux:select.option>
                                <flux:select.option value="Katargam">Katargam</flux:select.option>
                                <flux:select.option value="Varachha">Varachha</flux:select.option>
                                <flux:select.option value="Udhna - Sachin">Udhna - Sachin</flux:select.option>
                                <flux:select.option value="Dindoli">Dindoli</flux:select.option>
                                <flux:select.option value="Kamrej">Kamrej</flux:select.option>
                                <flux:select.option value="Saroli">Saroli</flux:select.option>
                                <flux:select.option value="Within City">Within City</flux:select.option>
                                <flux:select.option value="Outer City">Outer City</flux:select.option>
                                <flux:select.option value="Puna Kumbhaiya">Puna Kumbhaiya</flux:select.option>
                                <flux:select.option value="Others">Others</flux:select.option>
                            </flux:select>
                        </div>

                        @if (isset($projects[$index]['area']) && $projects[$index]['area'] === 'Others')
                            <flux:input wire:model="projects.{{ $index }}.area_other"
                                placeholder="Enter custom area/location" label="Custom Area" required />
                        @endif

                        <flux:input wire:model="projects.{{ $index }}.sq_ft" label="Total Area (sq ft)"
                            type="number" placeholder="0" required />

                        <!-- Project Category -->
                        <flux:radio.group wire:model="projects.{{ $index }}.category" label="Property Types"
                            variant="cards" class="max-sm:flex-col">
                            <flux:radio value="Residential" label="Residential" />
                            <flux:radio value="Commercial" label="Commercial" />
                            <flux:radio value="Plotting" label="Plotting" />
                        </flux:radio.group>
                        @error('projects.' . $index . '.category')
                            <flux:text class="mt-2 text-sm text-red-600 dark:text-red-400">
                                {{ $message }}
                            </flux:text>
                        @enderror

                        <!-- Budget Range -->
                        <flux:radio.group wire:model="projects.{{ $index }}.budget_range" label="Budget Range"
                            variant="cards" class="max-sm:flex-col">
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
                        <flux:radio.group wire:model="projects.{{ $index }}.status" label="Project Status"
                            variant="cards" class="max-sm:flex-col">
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

                        <flux:textarea wire:model="projects.{{ $index }}.usp" label="Unique Selling Points"
                            placeholder="Key features and highlights..." rows="3" required />

                        <flux:input wire:model="projects.{{ $index }}.contact_person"
                            label="Project Contact Person" type="text"
                            placeholder="Name of person handling this project" required />

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
                                    <flux:label>Project Brochure/PDF</flux:label>

                                    <div class="relative">
                                        <input type="file" @change="handleFile($event)" accept="application/pdf"
                                            class="hidden" id="project-{{ $index }}-pdf-upload"
                                            :disabled="uploading || {{ isset($project['pdf']) ? 'true' : 'false' }}" />
                                        <label for="project-{{ $index }}-pdf-upload"
                                            @dragover.prevent="isDragging = true"
                                            @dragleave.prevent="isDragging = false" @drop.prevent="handleDrop($event)"
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
                                            <flux:icon.document class="mb-2 size-8 text-zinc-400 dark:text-zinc-600" />
                                            <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300"
                                                x-show="!uploading">
                                                Drop PDF or browse
                                            </span>
                                            <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300"
                                                x-show="uploading" x-cloak>
                                                Uploading...
                                            </span>
                                        </label>
                                    </div>

                                    @if (isset($project['pdf']))
                                        <div class="mt-2">
                                            <flux:file-item :heading="$project['pdf']->getClientOriginalName()"
                                                :size="$project['pdf']->getSize()">
                                                <x-slot name="actions">
                                                    <flux:file-item.remove
                                                        wire:click="removeProjectFile({{ $index }}, 'pdf')"
                                                        aria-label="Remove PDF" />
                                                </x-slot>
                                            </flux:file-item>
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
                                    <flux:label>Project Logo</flux:label>

                                    <div class="relative">
                                        <input type="file" @change="handleFile($event)"
                                            accept="image/png,image/jpeg,image/jpg" class="hidden"
                                            id="project-{{ $index }}-logo-upload"
                                            :disabled="uploading || {{ isset($project['logo']) ? 'true' : 'false' }}" />
                                        <label for="project-{{ $index }}-logo-upload"
                                            @dragover.prevent="isDragging = true"
                                            @dragleave.prevent="isDragging = false" @drop.prevent="handleDrop($event)"
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
                                            <flux:icon.photo class="mb-2 size-8 text-zinc-400 dark:text-zinc-600" />
                                            <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300"
                                                x-show="!uploading">
                                                Drop image or browse
                                            </span>
                                            <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300"
                                                x-show="uploading" x-cloak>
                                                Uploading...
                                            </span>
                                        </label>
                                    </div>

                                    @if (isset($project['logo']))
                                        <div class="mt-2">
                                            <flux:file-item :heading="$project['logo']->getClientOriginalName()"
                                                :image="$project['logo']->temporaryUrl()"
                                                :size="$project['logo']->getSize()">
                                                <x-slot name="actions">
                                                    <flux:file-item.remove
                                                        wire:click="removeProjectFile({{ $index }}, 'logo')"
                                                        aria-label="Remove logo" />
                                                </x-slot>
                                            </flux:file-item>
                                        </div>
                                    @endif

                                    @error('projects.' . $index . '.logo')
                                        <flux:text class="text-sm text-red-600 dark:text-red-400">
                                            {{ $message }}</flux:text>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <flux:input wire:model="projects.{{ $index }}.video_url" label="Project Video URL"
                            type="url" placeholder="https://www.youtube.com/watch?v=..." badge="Optional" />

                        <!-- Project Photos (3-5) -->
                        <div x-data="{
                            uploading: false,
                            isDragging: false,
                            handleFile(event) {
                                const file = event.target.files[0];
                                if (file) {
                                    this.uploading = true;
                                    @this.upload('projects.{{ $index }}.newPhoto', file, () => {
                                        this.uploading = false;
                                        event.target.value = '';
                                    }, () => {
                                        this.uploading = false;
                                        event.target.value = '';
                                    })
                                }
                            },
                            handleDrop(event) {
                                event.preventDefault();
                                this.isDragging = false;
                                const file = event.dataTransfer.files[0];
                                if (file && file.type.match(/^image\/(png|jpeg|jpg)$/)) {
                                    this.uploading = true;
                                    @this.upload('projects.{{ $index }}.newPhoto', file, () => {
                                        this.uploading = false;
                                    }, () => {
                                        this.uploading = false;
                                    })
                                }
                            }
                        }">
                            <div class="space-y-2">
                                <flux:label>Project Photos (3-5 images)</flux:label>
                                @if (isset($project['photos']) && is_array($project['photos']))
                                    <flux:text class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                        {{ count($project['photos']) }}/5 photos uploaded
                                    </flux:text>
                                @endif
                            </div>

                            <div class="relative mt-3">
                                <input type="file" @change="handleFile($event)"
                                    accept="image/png,image/jpeg,image/jpg" class="hidden"
                                    id="project-{{ $index }}-photos-upload" :disabled="uploading" />
                                <label for="project-{{ $index }}-photos-upload"
                                    @dragover.prevent="isDragging = true" @dragleave.prevent="isDragging = false"
                                    @drop.prevent="handleDrop($event)"
                                    class="{{ implode(' ', [
                                        'flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed',
                                        'border-zinc-300 bg-zinc-50 px-4 py-6 transition hover:border-zinc-400',
                                        'dark:border-zinc-700 dark:bg-zinc-900 dark:hover:border-zinc-600',
                                    ]) }}"
                                    :class="{
                                        'cursor-not-allowed opacity-60': uploading,
                                        'border-blue-400 bg-blue-50 dark:border-blue-500 dark:bg-blue-900/20': isDragging
                                    }">
                                    <flux:icon.photo class="mb-2 size-8 text-zinc-400 dark:text-zinc-600" />
                                    <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300"
                                        x-show="!uploading">
                                        Drop photos or click to browse
                                    </span>
                                    <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300"
                                        x-show="uploading" x-cloak>
                                        Uploading...
                                    </span>
                                    <span class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                        PNG, JPG, or JPEG, 3-5 files
                                    </span>
                                </label>
                            </div>

                            @if (isset($project['photos']) && is_array($project['photos']) && count($project['photos']) > 0)
                                <div class="mt-3 grid grid-cols-2 gap-3">
                                    @foreach ($project['photos'] as $photoIndex => $photo)
                                        <div
                                            class="relative rounded-lg border border-zinc-200 p-2 dark:border-zinc-700">
                                            <flux:file-item :heading="$photo->getClientOriginalName()"
                                                :image="$photo->temporaryUrl()" :size="$photo->getSize()">
                                                <x-slot name="actions">
                                                    <flux:file-item.remove
                                                        wire:click="removeProjectPhoto({{ $index }}, {{ $photoIndex }})"
                                                        aria-label="Remove photo" />
                                                </x-slot>
                                            </flux:file-item>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @error('projects.' . $index . '.photos')
                                <flux:text class="mt-2 text-sm text-red-600 dark:text-red-400">
                                    {{ $message }}
                                </flux:text>
                            @enderror
                        </div>
                    </div>

                    @error('projects.' . $index)
                        <flux:text class="mt-4 text-sm text-red-600 dark:text-red-400">
                            {{ $message }}</flux:text>
                    @enderror
                </div>
            @endforeach
        @else
            <div class="rounded-lg border border-dashed border-zinc-300 p-8 text-center dark:border-zinc-700">
                <flux:icon.cube class="mx-auto mb-3 size-12 text-zinc-400 dark:text-zinc-600" />
                <flux:heading size="base" class="mb-2">No projects added yet</flux:heading>
                <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">
                    Click "Add Project" below to start adding your real estate projects.
                </flux:text>
            </div>
        @endif

        <div class="flex justify-center pt-4">
            <flux:button variant="outline" wire:click="addProject" icon="plus">
                Add Project
            </flux:button>
        </div>
    </flux:card>
@endif
