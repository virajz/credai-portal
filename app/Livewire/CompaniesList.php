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
    public string $sortBy = 'company_name';

    #[Url]
    public string $sortDirection = 'asc';

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
        $this->reset(['search']);
        $this->sortBy = 'company_name';
        $this->sortDirection = 'asc';
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

    public function generateAndCopyLink(int $companyId): void
    {
        $company = Company::findOrFail($companyId);

        if (! $company->registration_token) {
            $company->update([
                'registration_token' => Company::generateRegistrationToken(),
            ]);
        }

        $this->dispatch('copy-to-clipboard', url: $company->fresh()->registration_url);

        Flux::toast(
            heading: 'Link copied!',
            text: 'Registration link has been copied to clipboard.',
            variant: 'success'
        );
    }

    public function toggleLock(int $companyId): void
    {
        $company = Company::findOrFail($companyId);

        if ($company->is_locked) {
            $company->unlockRegistration();
            Flux::toast(
                heading: 'Link unlocked',
                text: "{$company->company_name} can now register using their link.",
                variant: 'success'
            );
        } else {
            $company->lockRegistration();
            Flux::toast(
                heading: 'Link locked',
                text: "{$company->company_name} can no longer register using their link.",
                variant: 'warning'
            );
        }
    }

    #[Title('Companies')]
    public function render()
    {
        $companies = Company::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('company_name', 'ilike', "%{$this->search}%")
                        ->orWhere('main_person_name', 'ilike', "%{$this->search}%")
                        ->orWhere('registered_number', 'ilike', "%{$this->search}%")
                        ->orWhere('stall_number', 'ilike', "%{$this->search}%");
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);

        return view('livewire.companies-list', [
            'companies' => $companies,
        ]);
    }
}
