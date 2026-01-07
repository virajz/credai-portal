<?php

namespace App\Livewire;

use App\Jobs\SendWhatsAppMessage;
use App\Models\Visitor;
use App\Services\QrCodeService;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.front')]
class SearchVisitors extends Component
{
    public string $searchPhone = '';

    public ?Visitor $foundVisitor = null;

    public ?int $visitorToSendWhatsApp = null;

    public function updatedSearchPhone(): void
    {
        // Auto-search when 10 digits are entered
        if (strlen($this->searchPhone) === 10 && is_numeric($this->searchPhone)) {
            $this->searchVisitor();
        } elseif (strlen($this->searchPhone) !== 10) {
            $this->foundVisitor = null;
        }
    }

    public function searchVisitor(): void
    {
        if (strlen($this->searchPhone) === 10 && is_numeric($this->searchPhone)) {
            $this->foundVisitor = Visitor::where('phone', $this->searchPhone)->first();

            if (! $this->foundVisitor) {
                Flux::toast(
                    heading: 'Not Found',
                    text: 'No visitor found with this phone number.',
                    variant: 'warning'
                );
            }
        }
    }

    public function clearSearch(): void
    {
        $this->reset('searchPhone', 'foundVisitor');
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
                ],
                imageUrl: $imageUrl
            );

            $this->visitorToSendWhatsApp = null;
            $this->modal('send-whatsapp-confirmation')->close();

            Flux::toast(
                heading: 'WhatsApp message queued',
                text: 'The QR code will be sent to the visitor shortly.',
                variant: 'success'
            );
        }
    }

    public function cancelSendWhatsApp(): void
    {
        $this->visitorToSendWhatsApp = null;
        $this->modal('send-whatsapp-confirmation')->close();
    }

    public function downloadVisitorImage(): void
    {
        if ($this->foundVisitor) {
            $this->dispatch('download-visitor-image', visitorId: $this->foundVisitor->id);
        }
    }

    #[On('refresh-recent-visitors')]
    public function refreshRecentVisitors(): void
    {
        // This method is called by the Livewire polling to refresh recent visitors
        // The actual refresh happens in the render method
    }

    #[Title('Search Visitors - CREDAI Glam Property Show 2026')]
    public function render()
    {
        $recentVisitors = Visitor::query()
            ->latest()
            ->limit(20)
            ->get(['id', 'uuid', 'name', 'phone', 'created_at']);

        return view('livewire.search-visitors', [
            'recentVisitors' => $recentVisitors,
        ]);
    }
}
