<?php

namespace App\Livewire;

use App\Models\Exhibitor;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.front')]
class PropertyCategory extends Component
{
    public string $category = '';

    public ?string $subType = null;

    public function mount(?string $subType = null): void
    {
        // Extract category from route name
        $routeName = request()->route()->getName();
        $this->category = match ($routeName) {
            'properties.residential' => 'residential',
            'properties.commercial' => 'commercial',
            'properties.plotting' => 'plotting',
            'properties.weekend-home' => 'weekend-home',
            default => '',
        };

        $this->subType = $subType;
    }

    public function getPageTitleProperty(): string
    {
        if ($this->subType) {
            return $this->subType;
        }

        return match ($this->category) {
            'residential' => 'Residential Properties',
            'commercial' => 'Commercial Properties',
            'plotting' => 'Plotting',
            'weekend-home' => 'Weekend Home',
            default => 'Properties',
        };
    }

    #[Title('Properties - CREDAI Glam Property Show 2026')]
    public function render()
    {
        $query = Exhibitor::query()
            ->with(['company', 'projects'])
            ->whereHas('company', function ($q) {
                $q->where('category', 'Builders');
            });

        // Filter based on category and subtype
        if ($this->subType) {
            // Subtype filtering (2 BHK, 3 BHK, commercial-office, etc.)
            $query->whereHas('projects', function ($projectQuery) {
                if (in_array($this->subType, ['commercial-office', 'commercial-shop'])) {
                    $projectQuery->whereJsonContains('units', [['type' => $this->subType]]);
                } else {
                    // Residential bedroom types
                    $projectQuery->whereJsonContains('units', [['bedrooms' => $this->subType]]);
                }
            });
        } else {
            // Category filtering (Residential, Commercial, Plotting, Weekend Home)
            $categoryMap = [
                'residential' => 'Residential',
                'commercial' => 'Commercial',
                'plotting' => 'Plotting',
                'weekend-home' => 'Weekend Home & Others',
            ];

            if (isset($categoryMap[$this->category])) {
                $query->whereHas('projects', function ($projectQuery) use ($categoryMap) {
                    $projectQuery->where('category', $categoryMap[$this->category]);
                });
            }
        }

        // Sort by company name
        $query->join('companies', 'exhibitors.company_id', '=', 'companies.id')
            ->orderBy('companies.company_name', 'asc')
            ->select('exhibitors.*');

        $exhibitors = $query->get();

        return view('livewire.property-category', [
            'exhibitors' => $exhibitors,
        ]);
    }
}
