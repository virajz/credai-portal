<?php

namespace App\Livewire;

use App\Models\Company;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CompaniesList extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $sortBy = 'created_at';

    #[Url]
    public string $sortDirection = 'desc';

    public ?int $companyToDelete = null;

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
        $this->reset(['search', 'sortBy', 'sortDirection']);
        $this->resetPage();
    }

    public function confirmDelete(int $companyId): void
    {
        $this->companyToDelete = $companyId;
        $this->modal('delete-company')->show();
    }

    public function deleteCompany(): void
    {
        if ($this->companyToDelete) {
            $company = Company::findOrFail($this->companyToDelete);
            $companyName = $company->company_name;

            $company->delete();

            $this->companyToDelete = null;
            $this->modal('delete-company')->close();

            Flux::toast(
                heading: 'Company deleted',
                text: "{$companyName} has been removed successfully.",
                variant: 'success'
            );
        }
    }

    public function cancelDelete(): void
    {
        $this->companyToDelete = null;
        $this->modal('delete-company')->close();
    }

    #[Title('Companies')]
    public function render()
    {
        $companies = Company::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('company_name', 'like', "%{$this->search}%")
                        ->orWhere('main_person_name', 'like', "%{$this->search}%")
                        ->orWhere('registered_number', 'like', "%{$this->search}%")
                        ->orWhere('stall_number', 'like', "%{$this->search}%");
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);

        return view('livewire.companies-list', [
            'companies' => $companies,
        ]);
    }
}
