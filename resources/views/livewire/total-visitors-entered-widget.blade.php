<div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800">
    <div class="absolute inset-0 flex flex-col p-4">
        <div class="mb-2 flex items-start justify-between">
            <div class="flex-1">
                <flux:heading size="lg" class="text-neutral-900 dark:text-neutral-100">Visitors Entered</flux:heading>
                <flux:text class="text-xs text-neutral-600 dark:text-neutral-400">Last 7 days</flux:text>
            </div>
            <div class="flex items-center gap-1.5">
                <flux:icon.user-group class="size-4 text-neutral-500 dark:text-neutral-400" />
                <flux:text class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">
                    {{ array_sum(array_column($this->chartData, 'visitors')) }}
                </flux:text>
            </div>
        </div>

        <flux:chart :value="$this->chartData" class="h-full flex-1">
            <flux:chart.svg>
                <flux:chart.area field="visitors" class="text-green-200/50 dark:text-green-400/30" curve="smooth" />
                <flux:chart.line field="visitors" class="text-green-500 dark:text-green-400" curve="smooth" />
                <flux:chart.point field="visitors" class="text-green-500 dark:text-green-400" r="3" stroke-width="2" />

                <flux:chart.axis axis="x" field="date" :format="[
                    'month' => 'short',
                    'day' => 'numeric'
                ]">
                    <flux:chart.axis.tick />
                    <flux:chart.axis.line />
                </flux:chart.axis>

                <flux:chart.axis axis="y" tick-start="0">
                    <flux:chart.axis.grid />
                    <flux:chart.axis.tick />
                </flux:chart.axis>

                <flux:chart.cursor />
            </flux:chart.svg>

            <flux:chart.tooltip>
                <flux:chart.tooltip.heading field="date" :format="[
                    'year' => 'numeric',
                    'month' => 'short',
                    'day' => 'numeric'
                ]" />
                <flux:chart.tooltip.value field="visitors" label="Entries" />
            </flux:chart.tooltip>
        </flux:chart>
    </div>
</div>
