<div x-data="{ isExporting: false }">
    <flux:button variant="primary" icon="arrow-down-tray" icon:variant="outline" wire:click="exportAll"
        x-on:click="isExporting = true"
        x-on:export-complete.window="isExporting = false"
        x-bind:disabled="isExporting">
        <span x-show="!isExporting">Export All</span>
        <span x-show="isExporting">Exporting...</span>
    </flux:button>
</div>
