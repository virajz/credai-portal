<div x-data="{
    uploading: false,
    isDragging: false,
    handleFiles(event) {
        const files = Array.from(event.target.files);
        if (files.length > 0) {
            this.uploading = true;
            @this.uploadMultiple('newPhotos', files, () => {
                this.uploading = false;
            }, () => {
                this.uploading = false;
            })
        }
    },
    handleDrop(event) {
        event.preventDefault();
        this.isDragging = false;
        const files = Array.from(event.dataTransfer.files).filter(file =>
            file.type.match(/^image\/(png|jpeg|jpg)$/)
        );
        if (files.length > 0) {
            this.uploading = true;
            @this.uploadMultiple('newPhotos', files, () => {
                this.uploading = false;
            }, () => {
                this.uploading = false;
            })
        }
    }
}">
    <div class="space-y-2">
        <flux:label>Upload Photos</flux:label>
        @if (is_array($photos) && count($photos) > 0)
            <flux:text class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                {{ count($photos) }}/5 photos uploaded (minimum 3 required)
            </flux:text>
        @endif
    </div>

    <div class="relative mt-3">
        <input type="file" @change="handleFiles($event)" accept="image/png,image/jpeg,image/jpg" class="hidden"
            id="company-photos-upload" multiple :disabled="uploading" />
        <label for="company-photos-upload" @dragover.prevent="isDragging = true" @dragleave.prevent="isDragging = false"
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
            <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300" x-show="!uploading">
                Drop photos or click to browse
            </span>
            <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300" x-show="uploading" x-cloak>
                Uploading...
            </span>
            <span class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                PNG, JPG, or JPEG, 3-5 files
            </span>
        </label>
    </div>

    @if (is_array($photos) && count($photos) > 0)
        <div class="mt-3 space-y-3">
            @foreach ($photos as $index => $photo)
                <div class="rounded-lg border border-zinc-200 p-3 dark:border-zinc-700">
                    <flux:file-item :heading="$photo->getClientOriginalName()" :image="$photo->temporaryUrl()"
                        :size="$photo->getSize()">
                        <x-slot name="actions">
                            <flux:file-item.remove wire:click="removePhoto({{ $index }})"
                                aria-label="Remove photo" />
                        </x-slot>
                    </flux:file-item>

                    <div class="mt-2">
                        <flux:input wire:model="photoLabels.{{ $index }}"
                            placeholder="e.g., Modern Villa, Office Exterior, Team Photo..." label="Photo Label" />
                    </div>

                    @error('photoLabels.' . $index)
                        <flux:text class="text-sm text-red-600 dark:text-red-400">{{ $message }}
                        </flux:text>
                    @enderror
                </div>
            @endforeach
        </div>
    @endif

    @error('photos')
        <flux:text class="mt-2 text-sm text-red-600 dark:text-red-400">
            {{ $message }}
        </flux:text>
    @enderror
</div>
