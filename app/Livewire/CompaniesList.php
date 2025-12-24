<?php

namespace App\Livewire;

use App\Models\ActivityLog;
use App\Models\Company;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    // Filter properties
    public bool $filterDrawerOpen = false;

    #[Url]
    public ?string $filterStatus = null;

    #[Url]
    public ?string $filterLockStatus = null;

    #[Url]
    public ?string $filterStallAssignment = null;

    #[Url]
    public ?string $filterCategory = null;

    #[Url]
    public ?string $filterLogoStatus = null;

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
        $this->reset(['search', 'filterStatus', 'filterLockStatus', 'filterStallAssignment', 'filterCategory', 'filterLogoStatus']);
        $this->sortBy = 'company_name';
        $this->sortDirection = 'asc';
        $this->resetPage();
    }

    public function toggleFilterDrawer(): void
    {
        $this->filterDrawerOpen = ! $this->filterDrawerOpen;
    }

    public function applyFilters(): void
    {
        $this->resetPage();
        $this->filterDrawerOpen = false;
    }

    public function hasActiveFilters(): bool
    {
        return $this->filterStatus !== null
            || $this->filterLockStatus !== null
            || $this->filterStallAssignment !== null
            || $this->filterCategory !== null
            || $this->filterLogoStatus !== null
            || ! empty($this->search);
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

            ActivityLog::log(
                'company_deleted',
                "Deleted company: {$companyName}",
                ['company_id' => $company->id, 'company_name' => $companyName]
            );

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

            ActivityLog::log(
                'registration_link_generated',
                "Generated registration link for: {$company->company_name}",
                ['company_id' => $company->id, 'company_name' => $company->company_name]
            );
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

            ActivityLog::log(
                'company_unlocked',
                "Unlocked registration for: {$company->company_name}",
                ['company_id' => $company->id, 'company_name' => $company->company_name]
            );

            Flux::toast(
                heading: 'Link unlocked',
                text: "{$company->company_name} can now register using their link.",
                variant: 'success'
            );
        } else {
            $company->lockRegistration();

            ActivityLog::log(
                'company_locked',
                "Locked registration for: {$company->company_name}",
                ['company_id' => $company->id, 'company_name' => $company->company_name]
            );

            Flux::toast(
                heading: 'Link locked',
                text: "{$company->company_name} can no longer register using their link.",
                variant: 'warning'
            );
        }
    }

    public function exportCompanies(): StreamedResponse
    {
        $companies = $this->getFilteredCompanies()->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="companies-'.now()->format('Y-m-d').'.csv"',
        ];

        $callback = function () use ($companies) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                'Company Name',
                'Main Person',
                'Registered Number',
                'Stall Number',
                'Stall Type',
                'Stall Size',
                'Status',
                'Lock Status',
                'Registration Link',
                'Created At',
                'Submitted At',
            ]);

            // Add rows
            foreach ($companies as $company) {
                // Ensure the company has a registration token
                if (! $company->registration_token) {
                    $company->update([
                        'registration_token' => Company::generateRegistrationToken(),
                    ]);
                    $company->refresh();
                }

                fputcsv($file, [
                    $company->company_name,
                    $company->main_person_name,
                    $company->registered_number,
                    $company->stall_number ?? 'Not assigned',
                    $company->stall_type ?? '',
                    $company->stall_size ?? '',
                    $company->has_submitted ? 'Submitted' : 'Pending',
                    $company->is_locked ? 'Locked' : 'Unlocked',
                    $company->registration_url,
                    $company->created_at->format('Y-m-d H:i:s'),
                    $company->submitted_at?->format('Y-m-d H:i:s') ?? '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    protected function getFilteredCompanies()
    {
        return Company::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('company_name', 'ilike', "%{$this->search}%")
                        ->orWhere('main_person_name', 'ilike', "%{$this->search}%")
                        ->orWhere('registered_number', 'ilike', "%{$this->search}%")
                        ->orWhere('stall_number', 'ilike', "%{$this->search}%");
                });
            })
            ->when($this->filterStatus !== null, function ($query) {
                if ($this->filterStatus === 'submitted') {
                    $query->where('has_submitted', true);
                } elseif ($this->filterStatus === 'pending') {
                    $query->where('has_submitted', false);
                }
            })
            ->when($this->filterLockStatus !== null, function ($query) {
                if ($this->filterLockStatus === 'locked') {
                    $query->where('is_locked', true);
                } elseif ($this->filterLockStatus === 'unlocked') {
                    $query->where('is_locked', false);
                }
            })
            ->when($this->filterStallAssignment !== null, function ($query) {
                if ($this->filterStallAssignment === 'assigned') {
                    $query->whereNotNull('stall_number');
                } elseif ($this->filterStallAssignment === 'unassigned') {
                    $query->whereNull('stall_number');
                }
            })
            ->when($this->filterCategory !== null, function ($query) {
                if ($this->filterCategory === 'builders') {
                    $query->where('category', 'Builders');
                } elseif ($this->filterCategory === 'allied') {
                    $query->where('category', 'Allied');
                }
            })
            ->when($this->filterLogoStatus !== null, function ($query) {
                if ($this->filterLogoStatus === 'with_logo') {
                    $query->whereHas('exhibitor', function ($q) {
                        $q->whereNotNull('logo_path');
                    });
                } elseif ($this->filterLogoStatus === 'without_logo') {
                    $query->where(function ($q) {
                        $q->whereDoesntHave('exhibitor')
                            ->orWhereHas('exhibitor', function ($subQ) {
                                $subQ->whereNull('logo_path');
                            });
                    });
                } elseif ($this->filterLogoStatus === 'no_submission') {
                    $query->where('has_submitted', false);
                }
            })
            ->orderBy($this->sortBy, $this->sortDirection);
    }

    #[Title('Companies')]
    public function render()
    {
        $companies = $this->getFilteredCompanies()->paginate(10);

        return view('livewire.companies-list', [
            'companies' => $companies,
        ]);
    }
}
