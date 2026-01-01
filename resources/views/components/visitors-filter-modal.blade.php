@props(['filterOptions'])

<flux:modal name="filters-modal" variant="flyout" class="max-w-md" @close="closeFiltersModal" wire:model="showFiltersModal">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Advanced Filters</flux:heading>
            <flux:subheading>Filter visitors by property interests and preferences</flux:subheading>
        </div>

        <div class="space-y-6 overflow-y-auto" style="max-height: calc(100vh - 200px);">
            @if (!empty($filterOptions['interests']))
                <flux:field>
                    <flux:label>Property Type (Interests)</flux:label>
                    <flux:select wire:model.live="selectedInterests" variant="listbox" multiple searchable placeholder="Select property types...">
                        @foreach ($filterOptions['interests'] as $interest)
                            <flux:select.option value="{{ $interest }}">{{ $interest }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:description>Filter by visitor property interests</flux:description>
                </flux:field>
            @endif

            @if (!empty($filterOptions['residentialTypes']))
                <flux:field>
                    <flux:label>Residential Sub Types</flux:label>
                    <flux:select wire:model.live="selectedResidentialTypes" variant="listbox" multiple searchable placeholder="Select residential types...">
                        @foreach ($filterOptions['residentialTypes'] as $type)
                            <flux:select.option value="{{ $type }}">{{ $type }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:description>Filter by residential property types</flux:description>
                </flux:field>
            @endif

            @if (!empty($filterOptions['commercialTypes']))
                <flux:field>
                    <flux:label>Commercial Sub Types</flux:label>
                    <flux:select wire:model.live="selectedCommercialTypes" variant="listbox" multiple searchable placeholder="Select commercial types...">
                        @foreach ($filterOptions['commercialTypes'] as $type)
                            <flux:select.option value="{{ $type }}">{{ $type }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:description>Filter by commercial property types</flux:description>
                </flux:field>
            @endif

            @if (!empty($filterOptions['plottingTypes']))
                <flux:field>
                    <flux:label>Plotting Sub Types</flux:label>
                    <flux:select wire:model.live="selectedPlottingTypes" variant="listbox" multiple searchable placeholder="Select plotting types...">
                        @foreach ($filterOptions['plottingTypes'] as $type)
                            <flux:select.option value="{{ $type }}">{{ $type }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:description>Filter by plotting types</flux:description>
                </flux:field>
            @endif

            @if (!empty($filterOptions['weekendHomeTypes']))
                <flux:field>
                    <flux:label>Weekend Home Sub Types</flux:label>
                    <flux:select wire:model.live="selectedWeekendHomeTypes" variant="listbox" multiple searchable placeholder="Select weekend home types...">
                        @foreach ($filterOptions['weekendHomeTypes'] as $type)
                            <flux:select.option value="{{ $type }}">{{ $type }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:description>Filter by weekend home types</flux:description>
                </flux:field>
            @endif

            @if (!empty($filterOptions['planningToBuy']))
                <flux:field>
                    <flux:label>Planning to Buy</flux:label>
                    <flux:select wire:model.live="selectedPlanningToBuy" variant="listbox" multiple searchable placeholder="Select timeframe...">
                        @foreach ($filterOptions['planningToBuy'] as $plan)
                            <flux:select.option value="{{ $plan }}">{{ $plan }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:description>Filter by purchase timeline</flux:description>
                </flux:field>
            @endif

            @if (!empty($filterOptions['areas']))
                <flux:field>
                    <flux:label>Preferred Areas</flux:label>
                    <flux:select wire:model.live="selectedAreas" variant="listbox" multiple searchable placeholder="Select areas...">
                        @foreach ($filterOptions['areas'] as $area)
                            <flux:select.option value="{{ $area }}">{{ $area }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:description>Filter by preferred locations</flux:description>
                </flux:field>
            @endif
        </div>

        <div class="flex gap-2 border-t border-zinc-200 pt-4 dark:border-zinc-700">
            <flux:button variant="ghost" wire:click="clearFilters" icon="x-mark">
                Clear All
            </flux:button>
            <flux:spacer />
            <flux:button variant="primary" wire:click="closeFiltersModal">
                Apply Filters
            </flux:button>
        </div>
    </div>
</flux:modal>
