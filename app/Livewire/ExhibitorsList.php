<?php

namespace App\Livewire;

use App\Models\Exhibitor;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ExhibitorsList extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $sortBy = 'created_at';

    #[Url]
    public string $sortDirection = 'desc';

    #[Url]
    public string $cityFilter = '';

    public ?int $exhibitorToDelete = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCityFilter(): void
    {
        $this->resetPage();
    }

    public function sortByColumn(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'cityFilter', 'sortBy', 'sortDirection']);
        $this->resetPage();
    }

    public function confirmDelete(int $exhibitorId): void
    {
        $this->exhibitorToDelete = $exhibitorId;
        $this->modal('delete-exhibitor')->show();
    }

    public function deleteExhibitor(): void
    {
        if ($this->exhibitorToDelete) {
            $exhibitor = Exhibitor::findOrFail($this->exhibitorToDelete);
            $brandName = $exhibitor->brand_name;

            $exhibitor->delete();

            $this->exhibitorToDelete = null;
            $this->modal('delete-exhibitor')->close();

            Flux::toast(
                heading: 'Exhibitor deleted',
                text: "{$brandName} has been removed successfully.",
                variant: 'success'
            );
        }
    }

    public function cancelDelete(): void
    {
        $this->exhibitorToDelete = null;
        $this->modal('delete-exhibitor')->close();
    }

    #[Title('Exhibitors')]
    public function render()
    {
        $exhibitors = Exhibitor::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('brand_name', 'like', "%{$this->search}%")
                        ->orWhere('contact_person_name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhere('phone_number', 'like', "%{$this->search}%")
                        ->orWhere('city', 'like', "%{$this->search}%");
                });
            })
            ->when($this->cityFilter, function ($query) {
                $query->where('city', $this->cityFilter);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);

        $cities = Exhibitor::query()
            ->select('city')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');

        return view('livewire.exhibitors-list', [
            'exhibitors' => $exhibitors,
            'cities' => $cities,
        ]);
    }
}
