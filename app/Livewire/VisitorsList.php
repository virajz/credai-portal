<?php

namespace App\Livewire;

use App\Concerns\VisitorFiltering;
use App\Jobs\SendWhatsAppMessage;
use App\Models\EntryExitLog;
use App\Models\Visitor;
use App\Services\QrCodeService;
use Flux\Flux;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class VisitorsList extends Component
{
    use VisitorFiltering, WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public array $selectedCampaigns = [];

    #[Url]
    public string $sortBy = 'created_at';

    #[Url]
    public string $sortDirection = 'desc';

    public ?int $visitorToDelete = null;

    public bool $showQrModal = false;

    public string $qrMedium = '';

    public string $qrCodeSvg = '';

    public string $generatedUrl = '';

    public bool $showVisitorQrModal = false;

    public ?Visitor $selectedVisitor = null;

    public string $visitorQrCodeSvg = '';

    public bool $showVisitorDetailsModal = false;

    public ?Visitor $visitorDetails = null;

    public ?int $visitorToSendWhatsApp = null;

    public ?Visitor $visitorForEntries = null;

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
        $this->resetVisitorFilters();
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

    public function openQrModal(): void
    {
        $this->showQrModal = true;
        $this->reset('qrMedium', 'qrCodeSvg', 'generatedUrl');
        $this->modal('generate-qr-code')->show();
    }

    public function generateQrCode(): void
    {
        $this->validate([
            'qrMedium' => 'required|string|max:255',
        ]);

        $this->generatedUrl = route('visitor.register', ['medium' => $this->qrMedium]);

        $qrCodeService = new QrCodeService;
        $this->qrCodeSvg = $qrCodeService->generate($this->generatedUrl);
    }

    public function closeQrModal(): void
    {
        $this->showQrModal = false;
        $this->reset('qrMedium', 'qrCodeSvg', 'generatedUrl');
        $this->modal('generate-qr-code')->close();
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

    public function confirmSendWhatsApp(int $visitorId): void
    {
        $this->visitorToSendWhatsApp = $visitorId;
        $this->modal('send-whatsapp-confirmation')->show();
    }

    public function sendWhatsApp(): void
    {
        if ($this->visitorToSendWhatsApp) {
            $visitor = Visitor::findOrFail($this->visitorToSendWhatsApp);

            $qrCodeService = new QrCodeService;
            $url = route('visitor.show', $visitor);
            $filename = 'visitor-'.$visitor->uuid;
            $imageUrl = $qrCodeService->withSize(512, 2)->saveWhatsAppQrImage($url, $filename);

            SendWhatsAppMessage::dispatch(
                name: $visitor->name,
                phoneNumber: $visitor->phone,
                templateName: 'user_registration_1_copy',
                data: [
                    $visitor->name,
                    'GLAM SURAT – Property Show 2026',
                    '9, 10, 11 January 2026',
                    'Vanita Vishram Ground, Surat',
                ],
                imageUrl: $imageUrl,
                buttonValue: 'https://property-show.credai-surat.com/',
                visitorId: $visitor->id
            );

            $this->visitorToSendWhatsApp = null;
            $this->modal('send-whatsapp-confirmation')->close();

            Flux::toast(
                heading: 'WhatsApp message queued',
                text: "Message will be sent to {$visitor->name} ({$visitor->phone})",
                variant: 'success'
            );
        }
    }

    public function loadEntryExitLogs(int $visitorId): void
    {
        $this->visitorForEntries = Visitor::with(['entryExitLogs' => function ($query) {
            $query->orderBy('entry_time', 'desc');
        }])->findOrFail($visitorId);
    }

    #[On('export-visitors-all')]
    public function exportVisitors(?string $date = null)
    {
        if ($date) {
            return $this->exportVisitorsByDate($date);
        }

        $query = Visitor::query()
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
            });

        $visitors = $this->applyVisitorFilters($query)
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

        $this->dispatch('export-complete');

        return response()->stream($callback, 200, $headers);
    }

    #[On('export-visitors-date-wise')]
    public function exportVisitorsByDate(string $date)
    {
        $visitorIds = EntryExitLog::whereNotNull('visitor_id');

        if ($date !== 'all') {
            $visitorIds->whereDate('entry_time', $date);
        }

        $visitorIds = $visitorIds->distinct('visitor_id')
            ->pluck('visitor_id')
            ->toArray();

        $visitors = Visitor::whereIn('id', $visitorIds)
            ->orderBy('name', 'asc')
            ->cursor();

        $dateLabel = $date === 'all' ? 'all-days' : $date;
        $filename = "visitors_entered_{$dateLabel}_".now()->format('Y-m-d_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($visitors) {
            $file = fopen('php://output', 'w');

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

        $this->dispatch('export-complete');

        return response()->stream($callback, 200, $headers);
    }

    #[Title('Visitors')]
    public function render()
    {
        $query = Visitor::query()
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
            });

        $visitors = $this->applyVisitorFilters($query)
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

        $filterOptions = $this->getFilterOptions();

        return view('livewire.visitors-list', [
            'visitors' => $visitors,
            'availableCampaigns' => $availableCampaigns,
            'filterOptions' => $filterOptions,
        ]);
    }
}
