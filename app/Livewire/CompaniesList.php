<?php

namespace App\Livewire;

use App\Models\ActivityLog;
use App\Models\Company;
use App\Services\QrCodeService;
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

    public bool $showCompanyQrModal = false;

    public ?Company $selectedCompany = null;

    public string $companyQrCodeSvg = '';

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

    public function showCompanyQrCode(int $companyId): void
    {
        $this->selectedCompany = Company::findOrFail($companyId);

        // Ensure the company has a UUID
        if (! $this->selectedCompany->uuid) {
            $this->selectedCompany->update([
                'uuid' => \Illuminate\Support\Str::uuid()->toString(),
            ]);
            $this->selectedCompany->refresh();
        }

        $url = $this->selectedCompany->qr_validation_url;

        $qrCodeService = new QrCodeService;
        $this->companyQrCodeSvg = $qrCodeService->generate($url);

        $this->showCompanyQrModal = true;
        $this->modal('company-qr-code')->show();

        ActivityLog::log(
            'company_qr_generated',
            "Generated QR code for: {$this->selectedCompany->company_name}",
            ['company_id' => $this->selectedCompany->id, 'company_name' => $this->selectedCompany->company_name]
        );
    }

    public function closeCompanyQrModal(): void
    {
        $this->showCompanyQrModal = false;
        $this->selectedCompany = null;
        $this->companyQrCodeSvg = '';
        $this->modal('company-qr-code')->close();
    }

    public function downloadAllQrCodes(): StreamedResponse
    {
        $companies = Company::all();

        $headers = [
            'Content-Type' => 'application/zip',
            'Content-Disposition' => 'attachment; filename="company-qr-codes-'.now()->format('Y-m-d').'.zip"',
        ];

        $callback = function () use ($companies) {
            $zip = new \ZipArchive;
            $zipFileName = tempnam(sys_get_temp_dir(), 'qr_codes_');

            if ($zip->open($zipFileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
                return;
            }

            $qrCodeService = new QrCodeService;

            foreach ($companies as $company) {
                // Ensure company has UUID
                if (! $company->uuid) {
                    $company->update(['uuid' => \Illuminate\Support\Str::uuid()->toString()]);
                    $company->refresh();
                }

                $url = $company->qr_validation_url;
                $svg = $qrCodeService->generate($url);

                // Create filename: {stallNumber}-{companyName}.svg
                $stallNumber = $company->stall_number ?: 'no-stall';
                $companyName = \Illuminate\Support\Str::slug($company->company_name);
                $filename = "{$stallNumber}-{$companyName}.svg";

                $zip->addFromString($filename, $svg);
            }

            $zip->close();

            readfile($zipFileName);
            unlink($zipFileName);
        };

        ActivityLog::log(
            'bulk_qr_download',
            'Downloaded all company QR codes',
            ['company_count' => $companies->count()]
        );

        return response()->stream($callback, 200, $headers);
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

    public function exportCategoryWiseList(): StreamedResponse
    {
        // Categories based on the UI provided
        $categories = [
            '2 BHK',
            '3 BHK',
            '4 BHK',
            '5+ BHK',
            'Showroom',
            'Office Space',
            'Plotting',
            'Weekend Home',
        ];

        $headers = [
            'Content-Type' => 'application/zip',
            'Content-Disposition' => 'attachment; filename="companies-by-category-'.now()->format('Y-m-d').'.zip"',
        ];

        $callback = function () use ($categories) {
            $zip = new \ZipArchive;
            $zipFileName = tempnam(sys_get_temp_dir(), 'companies_by_category_');

            if ($zip->open($zipFileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
                return;
            }

            foreach ($categories as $category) {
                // Find companies that have a project matching this subcategory label.
                // Prefer matching `units` JSON (bedrooms/type), fall back to project fields.
                $companies = Company::whereHas('exhibitor.projects', function ($q) use ($category) {
                    $residentialLabels = ['2 BHK', '3 BHK', '4 BHK', '5+ BHK'];

                    if (in_array($category, $residentialLabels, true)) {
                        $q->where(function ($sub) use ($category) {
                            $sub->whereJsonContains('units', [['bedrooms' => $category]])
                                ->orWhere('category', 'ilike', "%{$category}%")
                                ->orWhere('name', 'ilike', "%{$category}%")
                                ->orWhere('area', 'ilike', "%{$category}%");
                        });
                    } elseif ($category === 'Showroom') {
                        $q->where(function ($sub) use ($category) {
                            $sub->whereJsonContains('units', [['type' => 'commercial-shop']])
                                ->orWhere('category', 'ilike', '%commercial%')
                                ->orWhere('name', 'ilike', "%{$category}%")
                                ->orWhere('area', 'ilike', "%{$category}%");
                        });
                    } elseif ($category === 'Office Space') {
                        $q->where(function ($sub) use ($category) {
                            $sub->whereJsonContains('units', [['type' => 'commercial-office']])
                                ->orWhere('category', 'ilike', '%commercial%')
                                ->orWhere('name', 'ilike', "%{$category}%")
                                ->orWhere('area', 'ilike', "%{$category}%");
                        });
                    } elseif ($category === 'Plotting') {
                        $q->where(function ($sub) use ($category) {
                            $sub->where('category', 'ilike', '%plotting%')
                                ->orWhereJsonContains('units', [['type' => 'plotting']])
                                ->orWhere('name', 'ilike', "%{$category}%")
                                ->orWhere('area', 'ilike', "%{$category}%");
                        });
                    } elseif ($category === 'Weekend Home') {
                        $q->where(function ($sub) use ($category) {
                            $sub->where('category', 'ilike', '%weekend%')
                                ->orWhere('category', 'ilike', '%Weekend Home & Others%')
                                ->orWhereJsonContains('units', [['type' => 'weekend-home']])
                                ->orWhere('name', 'ilike', "%{$category}%")
                                ->orWhere('area', 'ilike', "%{$category}%");
                        });
                    } else {
                        // Generic fallback match
                        $q->where(function ($sub) use ($category) {
                            $sub->where('category', 'ilike', "%{$category}%")
                                ->orWhere('name', 'ilike', "%{$category}%")
                                ->orWhere('area', 'ilike', "%{$category}%");
                        });
                    }
                })->get();

                // Create CSV in memory
                $csv = fopen('php://temp', 'r+');
                // Header row: Company Name, Stall Number
                fputcsv($csv, ['Company Name', 'Stall Number']);

                foreach ($companies as $company) {
                    fputcsv($csv, [
                        $company->company_name,
                        $company->stall_number ?? '',
                    ]);
                }

                rewind($csv);
                $contents = stream_get_contents($csv);
                fclose($csv);

                // Add to zip with a safe filename
                $fileName = preg_replace('/[^A-Za-z0-9 _.-]/', '_', $category).'.csv';
                $zip->addFromString($fileName, $contents);
            }

            $zip->close();

            // Stream the zip file
            readfile($zipFileName);
            @unlink($zipFileName);
        };

        ActivityLog::log(
            'export_companies_by_category',
            'Exported companies grouped by project category',
            ['requested_by' => auth()->id() ?? null]
        );

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
            ->with('exhibitor.projects')
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
