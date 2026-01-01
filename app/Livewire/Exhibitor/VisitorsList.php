<?php

namespace App\Livewire\Exhibitor;

use App\Models\Visitor;
use App\Services\QrCodeService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.exhibitor')]
class VisitorsList extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public array $selectedCampaigns = [];

    #[Url]
    public string $sortBy = 'created_at';

    #[Url]
    public string $sortDirection = 'desc';

    public bool $showVisitorQrModal = false;

    public ?Visitor $selectedVisitor = null;

    public string $visitorQrCodeSvg = '';

    public bool $showVisitorDetailsModal = false;

    public ?Visitor $visitorDetails = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedCampaigns(): void
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
        $this->reset(['search', 'selectedCampaigns']);
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    public function showVisitorQrCode(int $visitorId): void
    {
        $this->selectedVisitor = Visitor::findOrFail($visitorId);
        $url = route('visitor.show', $this->selectedVisitor);

        $qrCodeService = new QrCodeService;
        $this->visitorQrCodeSvg = $qrCodeService->generate($url);

        $this->showVisitorQrModal = true;
        $this->modal('visitor-qr-code')->show();
    }

    public function closeVisitorQrModal(): void
    {
        $this->showVisitorQrModal = false;
        $this->selectedVisitor = null;
        $this->visitorQrCodeSvg = '';
        $this->modal('visitor-qr-code')->close();
    }

    public function showVisitorDetails(int $visitorId): void
    {
        $this->visitorDetails = Visitor::findOrFail($visitorId);
        $this->showVisitorDetailsModal = true;
        $this->modal('visitor-details')->show();
    }

    public function closeVisitorDetailsModal(): void
    {
        $this->showVisitorDetailsModal = false;
        $this->visitorDetails = null;
        $this->modal('visitor-details')->close();
    }

    public function exportVisitors()
    {
        $visitors = Visitor::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'ilike', "%{$this->search}%")
                        ->orWhere('phone', 'ilike', "%{$this->search}%")
                        ->orWhere('company_name', 'ilike', "%{$this->search}%")
                        ->orWhere('current_residential_area', 'ilike', "%{$this->search}%");
                });
            })
            ->when(! empty($this->selectedCampaigns), function ($query) {
                $query->whereIn('tracking_medium', $this->selectedCampaigns);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->cursor();

        $filename = 'visitors_'.now()->format('Y-m-d_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($visitors) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                'ID',
                'Name',
                'Phone',
                'Age Group',
                'Current Residential Area',
                'Company Name',
                'Interests',
                'Residential Types',
                'Commercial Types',
                'Plotting Types',
                'Weekend Home Types',
                'Planning to Buy',
                'Areas',
                'Tracking Medium',
                'Registered At',
            ]);

            // Add data rows
            foreach ($visitors as $visitor) {
                fputcsv($file, [
                    $visitor->id,
                    $visitor->name,
                    $visitor->phone,
                    $visitor->age_group,
                    $visitor->current_residential_area,
                    $visitor->company_name,
                    is_array($visitor->interests) ? implode(', ', $visitor->interests) : '',
                    is_array($visitor->residential_types) ? implode(', ', $visitor->residential_types) : '',
                    is_array($visitor->commercial_types) ? implode(', ', $visitor->commercial_types) : '',
                    is_array($visitor->plotting_types) ? implode(', ', $visitor->plotting_types) : '',
                    is_array($visitor->weekend_home_types) ? implode(', ', $visitor->weekend_home_types) : '',
                    $visitor->planning_to_buy,
                    is_array($visitor->areas) ? implode(', ', $visitor->areas) : '',
                    $visitor->tracking_medium,
                    $visitor->created_at?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    #[Title('Visitors')]
    public function render()
    {
        $visitors = Visitor::query()
            ->select(['id', 'name', 'phone', 'age_group', 'company_name', 'tracking_medium', 'interests', 'planning_to_buy', 'created_at'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'ilike', "%{$this->search}%")
                        ->orWhere('phone', 'ilike', "%{$this->search}%")
                        ->orWhere('company_name', 'ilike', "%{$this->search}%")
                        ->orWhere('current_residential_area', 'ilike', "%{$this->search}%");
                });
            })
            ->when(! empty($this->selectedCampaigns), function ($query) {
                $query->whereIn('tracking_medium', $this->selectedCampaigns);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(15);

        $availableCampaigns = cache()->remember('available_campaigns', 300, function () {
            return Visitor::select('tracking_medium')
                ->distinct()
                ->whereNotNull('tracking_medium')
                ->where('tracking_medium', '!=', '')
                ->orderBy('tracking_medium')
                ->pluck('tracking_medium')
                ->toArray();
        });

        return view('livewire.exhibitor.visitors-list', [
            'visitors' => $visitors,
            'availableCampaigns' => $availableCampaigns,
        ]);
    }
}
