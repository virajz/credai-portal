<div class="space-y-6">
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <!-- Logo Upload -->
        <div x-data="{
            uploading: false,
            isDragging: false,
            handleFile(event) {
                const file = event.target.files[0];
                if (file) {
                    this.uploading = true;
                    @this.upload('logo', file, () => {
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
                if (file && (file.type.match(/^image\/(png|jpeg|jpg)$/) || file.type === 'application/pdf' || file.name.endsWith('.cdr'))) {
                    this.uploading = true;
                    @this.upload('logo', file, () => {
                        this.uploading = false;
                    }, () => {
                        this.uploading = false;
                    })
                }
            }
        }">
            <div class="space-y-2">
                <flux:label>Upload Logo</flux:label>

                <div class="relative">
                    <input type="file" @change="handleFile($event)"
                        accept="image/png,image/jpeg,image/jpg,application/pdf,.cdr,application/x-coreldraw,application/coreldraw"
                        class="hidden" id="logo-upload" :disabled="uploading || {{ $logo ? 'true' : 'false' }}" />
                    <label for="logo-upload" @dragover.prevent="isDragging = true"
                        @dragleave.prevent="isDragging = false" @drop.prevent="handleDrop($event)"
                        class="{{ implode(' ', [
                            'flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed',
                            'border-zinc-300 bg-zinc-50 px-6 py-8 transition hover:border-zinc-400',
                            'dark:border-zinc-700 dark:bg-zinc-900 dark:hover:border-zinc-600',
                        ]) }}"
                        :class="{
                            'cursor-not-allowed opacity-60': uploading ||
                                {{ $logo ? 'true' : 'false' }},
                            'border-blue-400 bg-blue-50 dark:border-blue-500 dark:bg-blue-900/20': isDragging
                        }">
                        <flux:icon.photo class="mb-3 size-10 text-zinc-400 dark:text-zinc-600" />
                        <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300" x-show="!uploading">
                            Drop file or click to browse
                        </span>
                        <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300" x-show="uploading" x-cloak>
                            Uploading...
                        </span>
                        <span class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                            PNG, JPG, PDF, or CDR, max 5MB
                        </span>
                    </label>
                </div>

                <div x-show="uploading" x-cloak
                    class="rounded-lg border border-blue-200 bg-blue-50 p-3 dark:border-blue-800 dark:bg-blue-900/20">
                    <div class="flex items-center justify-between text-sm">
                        <span class="font-medium text-blue-700 dark:text-blue-300">Uploading logo...</span>
                    </div>
                    <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-blue-200 dark:bg-blue-900">
                        <div class="h-full w-full animate-pulse bg-blue-600 dark:bg-blue-400"></div>
                    </div>
                </div>
            </div>

            @if ($logo)
                <div class="mt-3">
                    <flux:file-item :heading="$logo->getClientOriginalName()" :size="$logo->getSize()">
                        <x-slot name="actions">
                            <flux:file-item.remove wire:click="removeLogo" aria-label="Remove logo" />
                        </x-slot>
                    </flux:file-item>
                </div>
            @elseif (isset($logo_path) && $logo_path)
                <div class="mt-3">
                    <div
                        class="flex items-center gap-3 rounded-lg border border-zinc-200 bg-white p-3 dark:border-zinc-700 dark:bg-zinc-800">
                        <div class="rounded-md bg-blue-100 p-2 shrink-0 dark:bg-blue-900/30">
                            <flux:icon.photo class="size-5 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="truncate text-sm font-medium text-zinc-900 dark:text-white"
                                title="{{ preg_replace('/^\d+_/', '', basename($logo_path)) }}">
                                {{ preg_replace('/^\d+_/', '', basename($logo_path)) }}
                            </div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">Logo</div>
                        </div>
                        <div class="flex gap-1 shrink-0">
                            <flux:button size="sm" variant="ghost" href="{{ Storage::url($logo_path) }}"
                                download="{{ preg_replace('/^\d+_/', '', basename($logo_path)) }}" square
                                aria-label="Download logo">
                                <flux:icon.arrow-down-tray class="size-4" />
                            </flux:button>
                            <flux:button size="sm" variant="ghost" wire:click="removeLogo" square
                                aria-label="Replace logo">
                                <flux:icon.trash class="size-4" />
                            </flux:button>
                        </div>
                    </div>
                </div>
            @endif

            @error('logo')
                <flux:text class="text-sm text-red-600 dark:text-red-400">{{ $message }}</flux:text>
            @enderror
        </div>

        <!-- Brochure Upload -->
        <div x-data="{
            uploading: false,
            isDragging: false,
            handleFile(event) {
                const file = event.target.files[0];
                if (file) {
                    this.uploading = true;
                    @this.upload('brochure', file, () => {
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
                    @this.upload('brochure', file, () => {
                        this.uploading = false;
                    }, () => {
                        this.uploading = false;
                    })
                }
            }
        }">
            <div class="space-y-2">
                <flux:label>Company Brochure</flux:label>

                <div class="relative">
                    <input type="file" @change="handleFile($event)" accept="application/pdf" class="hidden"
                        id="brochure-upload" :disabled="uploading || {{ $brochure ? 'true' : 'false' }}" />
                    <label for="brochure-upload" @dragover.prevent="isDragging = true"
                        @dragleave.prevent="isDragging = false" @drop.prevent="handleDrop($event)"
                        class="{{ implode(' ', [
                            'flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed',
                            'border-zinc-300 bg-zinc-50 px-6 py-8 transition hover:border-zinc-400',
                            'dark:border-zinc-700 dark:bg-zinc-900 dark:hover:border-zinc-600',
                        ]) }}"
                        :class="{
                            'cursor-not-allowed opacity-60': uploading ||
                                {{ $brochure ? 'true' : 'false' }},
                            'border-blue-400 bg-blue-50 dark:border-blue-500 dark:bg-blue-900/20': isDragging
                        }">
                        <flux:icon.document class="mb-3 size-10 text-zinc-400 dark:text-zinc-600" />
                        <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300" x-show="!uploading">
                            Drop PDF or click to browse
                        </span>
                        <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300" x-show="uploading" x-cloak>
                            Uploading...
                        </span>
                        <span class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                            PDF only, max 10MB
                        </span>
                    </label>
                </div>

                <div x-show="uploading" x-cloak
                    class="rounded-lg border border-blue-200 bg-blue-50 p-3 dark:border-blue-800 dark:bg-blue-900/20">
                    <div class="flex items-center justify-between text-sm">
                        <span class="font-medium text-blue-700 dark:text-blue-300">Uploading brochure...</span>
                    </div>
                    <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-blue-200 dark:bg-blue-900">
                        <div class="h-full w-full animate-pulse bg-blue-600 dark:bg-blue-400"></div>
                    </div>
                </div>
            </div>

            @if ($brochure)
                <div class="mt-3">
                    <flux:file-item :heading="$brochure->getClientOriginalName()" :size="$brochure->getSize()">
                        <x-slot name="actions">
                            <flux:file-item.remove wire:click="removeBrochure" aria-label="Remove brochure" />
                        </x-slot>
                    </flux:file-item>
                </div>
            @elseif (isset($brochure_path) && $brochure_path)
                <div class="mt-3">
                    <div
                        class="flex items-center gap-3 rounded-lg border border-zinc-200 bg-white p-3 dark:border-zinc-700 dark:bg-zinc-800">
                        <div class="rounded-md bg-red-100 p-2 shrink-0 dark:bg-red-900/30">
                            <flux:icon.document class="size-5 text-red-600 dark:text-red-400" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="truncate text-sm font-medium text-zinc-900 dark:text-white"
                                title="{{ preg_replace('/^\d+_/', '', basename($brochure_path)) }}">
                                {{ preg_replace('/^\d+_/', '', basename($brochure_path)) }}
                            </div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">Brochure</div>
                        </div>
                        <div class="flex gap-1 shrink-0">
                            <flux:button size="sm" variant="ghost" href="{{ Storage::url($brochure_path) }}"
                                download="{{ preg_replace('/^\d+_/', '', basename($brochure_path)) }}" square
                                aria-label="Download brochure">
                                <flux:icon.arrow-down-tray class="size-4" />
                            </flux:button>
                            <flux:button size="sm" variant="ghost" wire:click="removeBrochure" square
                                aria-label="Replace brochure">
                                <flux:icon.trash class="size-4" />
                            </flux:button>
                        </div>
                    </div>
                </div>
            @endif

            @error('brochure')
                <flux:text class="text-sm text-red-600 dark:text-red-400">{{ $message }}</flux:text>
            @enderror
        </div>
    </div>

    <!-- Video URL -->
    <flux:input wire:model="video_url" label="Video URL (YouTube/Vimeo)" type="url"
        placeholder="https://www.youtube.com/watch?v=..." badge="Optional" />

    <!-- Social Media Links -->
    <div>
        <div class="mb-3 flex items-center gap-2">
            <flux:heading size="base">Social Media Links</flux:heading>
            <flux:badge size="sm" variant="outline" color="zinc">Optional</flux:badge>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <flux:input wire:model="social_media_links.facebook" label="Facebook" type="url"
                placeholder="https://facebook.com/yourcompany">
                <x-slot name="iconLeading">
                    <x-bi-facebook />
                </x-slot>
            </flux:input>

            <flux:input wire:model="social_media_links.instagram" label="Instagram" type="url"
                placeholder="https://instagram.com/yourcompany">
                <x-slot name="iconLeading">
                    <x-bi-instagram />
                </x-slot>
            </flux:input>

            <flux:input wire:model="social_media_links.linkedin" label="LinkedIn" type="url"
                placeholder="https://linkedin.com/company/yourcompany">
                <x-slot name="iconLeading">
                    <x-bi-linkedin />
                </x-slot>
            </flux:input>

            <flux:input wire:model="social_media_links.youtube" label="YouTube" type="url"
                placeholder="https://youtube.com/@yourcompany">
                <x-slot name="iconLeading">
                    <x-bi-youtube />
                </x-slot>
            </flux:input>
        </div>
    </div>

    <!-- Additional Details -->
    <flux:textarea wire:model="additional_details" label="Additional Details"
        placeholder="e.g., Established in 2010, Specializing in luxury villas..." rows="4" badge="Optional" />
</div>
