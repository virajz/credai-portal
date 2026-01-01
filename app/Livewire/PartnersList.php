<?php

namespace App\Livewire;

use App\Jobs\SendWhatsAppMessage;
use App\Models\Partner;
use App\Services\QrCodeService;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class PartnersList extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $sortBy = 'created_at';

    #[Url]
    public string $sortDirection = 'desc';

    public ?int $partnerToDelete = null;

    public bool $showPartnerQrModal = false;

    public ?Partner $selectedPartner = null;

    public string $partnerQrCodeSvg = '';

    public bool $showPartnerDetailsModal = false;

    public ?Partner $partnerDetails = null;

    public ?int $partnerToSendWhatsApp = null;

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

    public function confirmDelete(int $partnerId): void
    {
        $this->partnerToDelete = $partnerId;
        $this->modal('delete-partner')->show();
    }

    public function deletePartner(): void
    {
        if ($this->partnerToDelete) {
            $partner = Partner::findOrFail($this->partnerToDelete);
            $partnerName = $partner->first_name.' '.$partner->last_name;

            $partner->delete();

            $this->partnerToDelete = null;
            $this->modal('delete-partner')->close();

            Flux::toast(
                heading: 'Partner deleted',
                text: "{$partnerName} has been removed successfully.",
                variant: 'success'
            );
        }
    }

    public function cancelDelete(): void
    {
        $this->partnerToDelete = null;
        $this->modal('delete-partner')->close();
    }

    public function showPartnerQrCode(int $partnerId): void
    {
        $this->selectedPartner = Partner::findOrFail($partnerId);
        $url = route('partner.show', $this->selectedPartner);

        $qrCodeService = new QrCodeService;
        $this->partnerQrCodeSvg = $qrCodeService->generate($url);

        $this->showPartnerQrModal = true;
        $this->modal('partner-qr-code')->show();
    }

    public function closePartnerQrModal(): void
    {
        $this->showPartnerQrModal = false;
        $this->selectedPartner = null;
        $this->partnerQrCodeSvg = '';
        $this->modal('partner-qr-code')->close();
    }

    public function showPartnerDetails(int $partnerId): void
    {
        $this->partnerDetails = Partner::findOrFail($partnerId);
        $this->showPartnerDetailsModal = true;
        $this->modal('partner-details')->show();
    }

    public function closePartnerDetailsModal(): void
    {
        $this->showPartnerDetailsModal = false;
        $this->partnerDetails = null;
        $this->modal('partner-details')->close();
    }

    public function confirmSendWhatsApp(int $partnerId): void
    {
        $this->partnerToSendWhatsApp = $partnerId;
        $this->modal('send-whatsapp')->show();
    }

    public function sendWhatsApp(): void
    {
        if ($this->partnerToSendWhatsApp && auth()->user()->isAdmin()) {
            $partner = Partner::findOrFail($this->partnerToSendWhatsApp);

            $qrCodeService = new QrCodeService;
            $url = route('partner.show', $partner);
            $filename = 'partner-'.$partner->uuid;
            $imageUrl = $qrCodeService->withSize(512, 2)->saveWhatsAppQrImage($url, $filename);

            $partnerName = $partner->first_name.' '.$partner->last_name;

            SendWhatsAppMessage::dispatch(
                name: $partnerName,
                phoneNumber: $partner->phone,
                templateName: 'user_registration_1_copy',
                data: [
                    $partnerName,
                    'GLAM SURAT – Property Show 2026',
                    '9, 10, 11 January 2026',
                    'Vanita Vishram Ground, Surat',
                ],
                imageUrl: $imageUrl,
                buttonValue: 'https://property-show.credai-surat.com/',
            );

            $this->partnerToSendWhatsApp = null;
            $this->modal('send-whatsapp')->close();

            Flux::toast(
                heading: 'WhatsApp message queued',
                text: "Message will be sent to {$partnerName} shortly.",
                variant: 'success'
            );
        }
    }

    public function cancelSendWhatsApp(): void
    {
        $this->partnerToSendWhatsApp = null;
        $this->modal('send-whatsapp')->close();
    }

    public function exportData()
    {
        $partners = Partner::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('first_name', 'like', "%{$this->search}%")
                        ->orWhere('last_name', 'like', "%{$this->search}%")
                        ->orWhere('phone', 'like', "%{$this->search}%")
                        ->orWhere('firm_name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->get();

        $csv = "Name,Firm Name,Email,Phone,Property Types,Areas,Registered At\n";

        foreach ($partners as $partner) {
            $csv .= '"'.$partner->first_name.' '.$partner->last_name.'",';
            $csv .= '"'.($partner->firm_name ?? '').'",';
            $csv .= '"'.($partner->email ?? '').'",';
            $csv .= '"'.$partner->phone.'",';
            $csv .= '"'.implode(', ', $partner->property_types).'",';
            $csv .= '"'.implode(', ', $partner->areas).'",';
            $csv .= '"'.$partner->created_at->format('Y-m-d H:i:s').'"'."\n";
        }

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, 'partners-'.now()->format('Y-m-d').'.csv');
    }

    #[Title('Partners - CREDAI Glam Property Show 2026')]
    public function render()
    {
        $partners = Partner::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('first_name', 'like', "%{$this->search}%")
                        ->orWhere('last_name', 'like', "%{$this->search}%")
                        ->orWhere('phone', 'like', "%{$this->search}%")
                        ->orWhere('firm_name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(15);

        return view('livewire.partners-list', [
            'partners' => $partners,
        ]);
    }
}
