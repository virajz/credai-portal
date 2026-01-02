<?php

namespace App\Livewire;

use App\Models\Exhibitor;
use App\Models\Project;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.front')]
class PublicExhibitorsList extends Component
{
    use WithPagination;

    #[Url(as: 'search')]
    public string $search = '';

    #[Url(as: 'type')]
    public array $propertyType = [];

    #[Url(as: 'subtype')]
    public array $subType = [];

    #[Url(as: 'location')]
    public array $location = [];

    #[Url(as: 'price')]
    public array $priceRange = [];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingPropertyType(): void
    {
        $this->resetPage();
    }

    public function updatingSubType(): void
    {
        $this->resetPage();
    }

    public function updatingLocation(): void
    {
        $this->resetPage();
    }

    public function updatingPriceRange(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'propertyType', 'subType', 'location', 'priceRange']);
        $this->resetPage();
    }

    public function getAvailableLocations(): array
    {
        return Project::query()
            ->whereNotNull('area')
            ->distinct()
            ->pluck('area')
            ->sort()
            ->values()
            ->toArray();
    }

    public function getAvailablePriceRanges(): array
    {
        $ranges = [];

        // Get all projects
        $projects = Project::all();

        foreach ($projects as $project) {
            // Get prices from units (for Residential & Commercial)
            if (is_array($project->units)) {
                foreach ($project->units as $unit) {
                    if (! empty($unit['budget'])) {
                        $ranges[] = $unit['budget'];
                    }
                }
            }

            // Get prices from budget_range (for Plotting & Weekend Home & Others)
            if (! empty($project->budget_range)) {
                $ranges[] = $project->budget_range;
            }
        }

        // Remove duplicates and sort
        $ranges = array_unique($ranges);
        sort($ranges);

        return array_values($ranges);
    }

    #[Title('Exhibitors - CREDAI Glam Property Show 2026')]
    public function render()
    {
        $query = Exhibitor::query()
            ->with(['company', 'projects'])
            ->whereHas('company', function ($q) {
                $q->where('category', 'Builders');
            });

        // Search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('company', function ($companyQuery) {
                    $companyQuery->where('company_name', 'ilike', '%'.$this->search.'%');
                })
                    ->orWhere('city', 'ilike', '%'.$this->search.'%')
                    ->orWhere('office_address', 'ilike', '%'.$this->search.'%');
            });
        }

        // Property type filter (from projects)
        if (! empty($this->propertyType)) {
            $query->whereHas('projects', function ($projectQuery) {
                $projectQuery->whereIn('category', $this->propertyType);
            });
        }

        // Sub-type filter (from project units - bedrooms or commercial type)
        if (! empty($this->subType)) {
            $query->whereHas('projects', function ($projectQuery) {
                $projectQuery->where(function ($q) {
                    foreach ($this->subType as $subType) {
                        // Check if it's a commercial type
                        if (in_array($subType, ['commercial-office', 'commercial-shop'])) {
                            $q->orWhereJsonContains('units', [['type' => $subType]]);
                        } else {
                            // It's a residential bedroom type
                            $q->orWhereJsonContains('units', [['bedrooms' => $subType]]);
                        }
                    }
                });
            });
        }

        // Location filter (from projects)
        if (! empty($this->location)) {
            $query->whereHas('projects', function ($projectQuery) {
                $projectQuery->whereIn('area', $this->location);
            });
        }

        // Price range filter (from projects)
        if (! empty($this->priceRange)) {
            $query->whereHas('projects', function ($projectQuery) {
                $projectQuery->where(function ($q) {
                    foreach ($this->priceRange as $budget) {
                        // Check budget_range column (for Plotting & Weekend Home & Others)
                        $q->orWhere('budget_range', $budget)
                          // Check units column (for Residential & Commercial)
                            ->orWhereRaw('EXISTS (
                              SELECT 1 FROM json_array_elements(units) as unit
                              WHERE unit->>\'budget\' = ?
                          )', [$budget]);
                    }
                });
            });
        }

        // Sort by company name alphabetically
        $query->join('companies', 'exhibitors.company_id', '=', 'companies.id')
            ->orderBy('companies.company_name', 'asc')
            ->select('exhibitors.*');

        $exhibitors = $query->paginate(12);

        return view('livewire.public-exhibitors-list', [
            'exhibitors' => $exhibitors,
            'availableLocations' => $this->getAvailableLocations(),
            'availablePriceRanges' => $this->getAvailablePriceRanges(),
        ]);
    }
}
