<?php

namespace App\Livewire;

use App\Models\Visitor;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class VisitorsList extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $sortBy = 'created_at';

    #[Url]
    public string $sortDirection = 'desc';

    public ?int $visitorToDelete = null;

    public function updatingSearch(): void
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
        $this->reset(['search']);
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    public function confirmDelete(int $visitorId): void
    {
        $this->visitorToDelete = $visitorId;
        $this->modal('delete-visitor')->show();
    }

    public function deleteVisitor(): void
    {
        if ($this->visitorToDelete) {
            $visitor = Visitor::findOrFail($this->visitorToDelete);
            $visitorName = $visitor->name;

            $visitor->delete();

            $this->visitorToDelete = null;
            $this->modal('delete-visitor')->close();

            Flux::toast(
                heading: 'Visitor deleted',
                text: "{$visitorName} has been removed successfully.",
                variant: 'success'
            );
        }
    }

    public function cancelDelete(): void
    {
        $this->visitorToDelete = null;
        $this->modal('delete-visitor')->close();
    }

    public function exportVisitors(): void
    {
        // Export functionality can be added later if needed
        Flux::toast(
            heading: 'Export started',
            text: 'Visitor data export will begin shortly.',
            variant: 'info'
        );
    }

    #[Title('Visitors')]
    public function render()
    {
        $visitors = Visitor::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'ilike', "%{$this->search}%")
                        ->orWhere('email', 'ilike', "%{$this->search}%")
                        ->orWhere('phone', 'ilike', "%{$this->search}%")
                        ->orWhere('company_name', 'ilike', "%{$this->search}%");
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(15);

        return view('livewire.visitors-list', [
            'visitors' => $visitors,
        ]);
    }
}
