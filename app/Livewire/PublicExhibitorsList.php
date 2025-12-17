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
        $this->reset(['search', 'propertyType', 'location', 'priceRange']);
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
        return Project::query()
            ->whereNotNull('budget_range')
            ->distinct()
            ->pluck('budget_range')
            ->sort()
            ->values()
            ->toArray();
    }

    #[Title('Exhibitors - CREDAI Glam Property Show 2026')]
    public function render()
    {
        $query = Exhibitor::query()
            ->with(['company', 'projects']);

        // Search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('company', function ($companyQuery) {
                    $companyQuery->where('company_name', 'ilike', '%' . $this->search . '%');
                })
                    ->orWhere('city', 'ilike', '%' . $this->search . '%')
                    ->orWhere('office_address', 'ilike', '%' . $this->search . '%');
            });
        }

        // Property type filter (from projects)
        if (! empty($this->propertyType)) {
            $query->whereHas('projects', function ($projectQuery) {
                $projectQuery->whereIn('category', $this->propertyType);
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
                $projectQuery->whereIn('budget_range', $this->priceRange);
            });
        }

        $exhibitors = $query->paginate(12);

        return view('livewire.public-exhibitors-list', [
            'exhibitors' => $exhibitors,
            'availableLocations' => $this->getAvailableLocations(),
            'availablePriceRanges' => $this->getAvailablePriceRanges(),
        ]);
    }
}
