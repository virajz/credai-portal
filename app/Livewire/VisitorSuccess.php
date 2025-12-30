<?php

namespace App\Livewire;

use App\Jobs\SendWhatsAppMessage;
use App\Models\Visitor;
use App\Services\QrCodeService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.front')]
class VisitorSuccess extends Component
{
    public Visitor $visitor;

    public string $qrCodeSvg = '';

    public string $whatsappQrImage = '';

    public function mount(Visitor $visitor): void
    {
        $url = route('visitor.show', $visitor);

        $qrCodeService = new QrCodeService;
        $this->qrCodeSvg = $qrCodeService->generate($url);

        $imageData = $qrCodeService->withSize(512, 2)->overlayOnWhatsAppImage($url);
        $this->whatsappQrImage = 'data:image/png;base64,'.base64_encode($imageData);

        $this->dispatchWhatsAppMessage($visitor, $qrCodeService, $url);
    }

    protected function dispatchWhatsAppMessage(Visitor $visitor, QrCodeService $qrCodeService, string $url): void
    {
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
    }

    #[Title('Registration Successful - CREDAI Glam Property Show 2026')]
    public function render()
    {
        return view('livewire.visitor-success');
    }
}
