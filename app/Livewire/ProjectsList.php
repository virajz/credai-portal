<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Project;
use App\Services\ChatService;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ProjectsList extends Component
{
    use WithPagination;

    #[Url(as: 'search')]
    public string $searchQuery = '';

    #[Url]
    public string $category = '';

    #[Url]
    public string $area = '';

    #[Url]
    public string $bedrooms = '';

    #[Url]
    public string $status = '';

    #[Url]
    public string $budget = '';

    public bool $showFilters = false;

    public function toggleFilters(): void
    {
        $this->showFilters = ! $this->showFilters;
    }

    public function mount(): void
    {
        // If search query is provided, extract filters from it
        if ($this->searchQuery) {
            $this->extractFiltersFromSearch($this->searchQuery);
        }
    }

    public function updatedSearchQuery(): void
    {
        $this->resetPage();
        if ($this->searchQuery) {
            $this->extractFiltersFromSearch($this->searchQuery);
        }
    }

    public function updatedCategory(): void
    {
        $this->resetPage();
    }

    public function updatedArea(): void
    {
        $this->resetPage();
    }

    public function updatedBedrooms(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedBudget(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['searchQuery', 'category', 'area', 'bedrooms', 'status', 'budget']);
        $this->resetPage();
    }

    protected function extractFiltersFromSearch(string $message): void
    {
        $chatService = new ChatService;
        $params = $chatService->extractSearchParameters($message);

        if (! empty($params['category'])) {
            $this->category = $params['category'];
        }

        if (! empty($params['area'])) {
            $this->area = $params['area'];
        }

        if (! empty($params['bedrooms'])) {
            $this->bedrooms = $params['bedrooms'];
        }

        if (! empty($params['budget'])) {
            $this->budget = $params['budget'];
        }
    }

    public function render()
    {
        $query = Project::query()->with('exhibitor');

        // Apply filters
        if ($this->category) {
            $query->where('category', 'ILIKE', "%{$this->category}%");
        }

        if ($this->area) {
            $query->where('area', 'ILIKE', "%{$this->area}%");
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->bedrooms) {
            $query->whereRaw('EXISTS (
                SELECT 1 FROM json_array_elements(units) as unit
                WHERE unit->>\'bedrooms\' = ?
            )', [$this->bedrooms]);
        }

        if ($this->budget) {
            $query->where(function ($q) {
                $q->where('budget_range', 'ILIKE', "%{$this->budget}%")
                    ->orWhereRaw('EXISTS (
                      SELECT 1 FROM json_array_elements(units) as unit
                      WHERE unit->>\'budget\' ILIKE ?
                  )', ["%{$this->budget}%"]);
            });
        }

        $projects = $query->inRandomOrder()->paginate(12);

        // Get available filter options
        $categories = Project::query()
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category')
            ->unique()
            ->values();

        $areas = Project::query()
            ->distinct()
            ->whereNotNull('area')
            ->pluck('area')
            ->unique()
            ->values();

        $statuses = [
            'ongoing' => 'Ongoing',
            'ready_to_move' => 'Ready to Move',
            'completed' => 'Completed',
            'upcoming' => 'Upcoming',
        ];

        $bedroomOptions = ['1 BHK', '2 BHK', '3 BHK', '4 BHK', '5+ BHK'];

        return view('livewire.projects-list', [
            'projects' => $projects,
            'categories' => $categories,
            'areas' => $areas,
            'statuses' => $statuses,
            'bedroomOptions' => $bedroomOptions,
        ])->layout('components.layouts.front');
    }
}
