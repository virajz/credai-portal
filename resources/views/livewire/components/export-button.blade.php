<div x-data="{ isExporting: false }">
    <flux:button.group>
        <flux:button variant="primary" icon="arrow-down-tray" icon:variant="outline" wire:click="exportAll"
            x-on:click="isExporting = true"
            x-on:export-complete.window="isExporting = false"
            x-bind:disabled="isExporting">
            <span x-show="!isExporting">Export All</span>
            <span x-show="isExporting">Exporting...</span>
        </flux:button>

        <flux:dropdown position="top" align="end">
            <flux:button variant="primary" icon="chevron-down" icon:variant="outline" inset="left" />

            <flux:menu>
                <flux:menu.item wire:click="openDateModal" icon="calendar">Export by Date</flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </flux:button.group>

    <flux:modal name="date-export-modal" wire:model="showDateModal" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Export Visitors by Date</flux:heading>
                <flux:subheading class="mt-2">Select a date to export visitors who entered the exhibition</flux:subheading>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <flux:button
                    variant="{{ $selectedDate === '2026-01-09' ? 'primary' : 'ghost' }}"
                    wire:click="selectDate('2026-01-09')"
                    class="justify-center">
                    9th Jan
                </flux:button>

                <flux:button
                    variant="{{ $selectedDate === '2026-01-10' ? 'primary' : 'ghost' }}"
                    wire:click="selectDate('2026-01-10')"
                    class="justify-center">
                    10th Jan
                </flux:button>

                <flux:button
                    variant="{{ $selectedDate === '2026-01-11' ? 'primary' : 'ghost' }}"
                    wire:click="selectDate('2026-01-11')"
                    class="justify-center">
                    11th Jan
                </flux:button>

                <flux:button
                    variant="{{ $selectedDate === 'all' ? 'primary' : 'ghost' }}"
                    wire:click="selectDate('all')"
                    class="justify-center">
                    All Days
                </flux:button>
            </div>

            @if($selectedDate)
                <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-6 text-center dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Visitors Entered</div>
                    <div class="mt-2 text-4xl font-bold text-zinc-900 dark:text-white">{{ number_format($visitorCount) }}</div>
                </div>
            @endif

            <div class="flex gap-2">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="closeModal">Close</flux:button>
                <flux:button
                    variant="primary"
                    wire:click="exportDateWise"
                    x-bind:disabled="!$wire.selectedDate || isExporting"
                    x-on:click="if ($wire.selectedDate) { isExporting = true }"
                    x-on:export-complete.window="isExporting = false">
                    <span x-show="!isExporting">Export</span>
                    <span x-show="isExporting">Exporting...</span>
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
