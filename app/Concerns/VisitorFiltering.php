<?php

namespace App\Concerns;

use App\Models\Visitor;
use Livewire\Attributes\Url;

trait VisitorFiltering
{
    #[Url]
    public array $selectedInterests = [];

    #[Url]
    public array $selectedResidentialTypes = [];

    #[Url]
    public array $selectedCommercialTypes = [];

    #[Url]
    public array $selectedPlottingTypes = [];

    #[Url]
    public array $selectedWeekendHomeTypes = [];

    #[Url]
    public array $selectedPlanningToBuy = [];

    #[Url]
    public array $selectedAreas = [];

    public bool $showFiltersModal = false;

    public function updatingSelectedInterests(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedResidentialTypes(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedCommercialTypes(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedPlottingTypes(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedWeekendHomeTypes(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedPlanningToBuy(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedAreas(): void
    {
        $this->resetPage();
    }

    public function openFiltersModal(): void
    {
        $this->showFiltersModal = true;
        $this->modal('filters-modal')->show();
    }

    public function closeFiltersModal(): void
    {
        $this->showFiltersModal = false;
        $this->modal('filters-modal')->close();
    }

    protected function applyVisitorFilters($query)
    {
        return $query
            ->when(! empty($this->selectedInterests), function ($query) {
                $query->where(function ($q) {
                    foreach ($this->selectedInterests as $interest) {
                        $q->orWhereJsonContains('interests', $interest);
                    }
                });
            })
            ->when(! empty($this->selectedResidentialTypes), function ($query) {
                $query->where(function ($q) {
                    foreach ($this->selectedResidentialTypes as $type) {
                        $q->orWhereJsonContains('residential_types', $type);
                    }
                });
            })
            ->when(! empty($this->selectedCommercialTypes), function ($query) {
                $query->where(function ($q) {
                    foreach ($this->selectedCommercialTypes as $type) {
                        $q->orWhereJsonContains('commercial_types', $type);
                    }
                });
            })
            ->when(! empty($this->selectedPlottingTypes), function ($query) {
                $query->where(function ($q) {
                    foreach ($this->selectedPlottingTypes as $type) {
                        $q->orWhereJsonContains('plotting_types', $type);
                    }
                });
            })
            ->when(! empty($this->selectedWeekendHomeTypes), function ($query) {
                $query->where(function ($q) {
                    foreach ($this->selectedWeekendHomeTypes as $type) {
                        $q->orWhereJsonContains('weekend_home_types', $type);
                    }
                });
            })
            ->when(! empty($this->selectedPlanningToBuy), function ($query) {
                $query->whereIn('planning_to_buy', $this->selectedPlanningToBuy);
            })
            ->when(! empty($this->selectedAreas), function ($query) {
                $query->where(function ($q) {
                    foreach ($this->selectedAreas as $area) {
                        $q->orWhereJsonContains('areas', $area);
                    }
                });
            });
    }

    protected function getFilterOptions(): array
    {
        return cache()->remember('visitor_filter_options', 300, function () {
            $interests = Visitor::select('interests')
                ->whereNotNull('interests')
                ->get()
                ->pluck('interests')
                ->flatten()
                ->unique()
                ->filter()
                ->sort()
                ->values()
                ->toArray();

            $residentialTypes = Visitor::select('residential_types')
                ->whereNotNull('residential_types')
                ->get()
                ->pluck('residential_types')
                ->flatten()
                ->unique()
                ->filter()
                ->sort()
                ->values()
                ->toArray();

            $commercialTypes = Visitor::select('commercial_types')
                ->whereNotNull('commercial_types')
                ->get()
                ->pluck('commercial_types')
                ->flatten()
                ->unique()
                ->filter()
                ->sort()
                ->values()
                ->toArray();

            $plottingTypes = Visitor::select('plotting_types')
                ->whereNotNull('plotting_types')
                ->get()
                ->pluck('plotting_types')
                ->flatten()
                ->unique()
                ->filter()
                ->sort()
                ->values()
                ->toArray();

            $weekendHomeTypes = Visitor::select('weekend_home_types')
                ->whereNotNull('weekend_home_types')
                ->get()
                ->pluck('weekend_home_types')
                ->flatten()
                ->unique()
                ->filter()
                ->sort()
                ->values()
                ->toArray();

            $planningToBuy = Visitor::select('planning_to_buy')
                ->whereNotNull('planning_to_buy')
                ->where('planning_to_buy', '!=', '')
                ->distinct()
                ->orderBy('planning_to_buy')
                ->pluck('planning_to_buy')
                ->toArray();

            $areas = Visitor::select('areas')
                ->whereNotNull('areas')
                ->get()
                ->pluck('areas')
                ->flatten()
                ->unique()
                ->filter()
                ->sort()
                ->values()
                ->toArray();

            return [
                'interests' => $interests,
                'residentialTypes' => $residentialTypes,
                'commercialTypes' => $commercialTypes,
                'plottingTypes' => $plottingTypes,
                'weekendHomeTypes' => $weekendHomeTypes,
                'planningToBuy' => $planningToBuy,
                'areas' => $areas,
            ];
        });
    }

    protected function hasActiveFilters(): bool
    {
        return ! empty($this->selectedInterests)
            || ! empty($this->selectedResidentialTypes)
            || ! empty($this->selectedCommercialTypes)
            || ! empty($this->selectedPlottingTypes)
            || ! empty($this->selectedWeekendHomeTypes)
            || ! empty($this->selectedPlanningToBuy)
            || ! empty($this->selectedAreas);
    }

    protected function getActiveFilterCount(): int
    {
        return count($this->selectedInterests)
            + count($this->selectedResidentialTypes)
            + count($this->selectedCommercialTypes)
            + count($this->selectedPlottingTypes)
            + count($this->selectedWeekendHomeTypes)
            + count($this->selectedPlanningToBuy)
            + count($this->selectedAreas);
    }

    protected function resetVisitorFilters(): void
    {
        $this->selectedInterests = [];
        $this->selectedResidentialTypes = [];
        $this->selectedCommercialTypes = [];
        $this->selectedPlottingTypes = [];
        $this->selectedWeekendHomeTypes = [];
        $this->selectedPlanningToBuy = [];
        $this->selectedAreas = [];
    }
}
