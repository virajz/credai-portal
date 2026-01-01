<?php

namespace App\Livewire;

use App\Models\Partner;
use App\Services\QrCodeService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.front')]
class PartnerSuccess extends Component
{
    public Partner $partner;

    public string $qrCodeSvg = '';

    public string $whatsappQrImage = '';

    public function mount(Partner $partner): void
    {
        $url = route('partner.show', $partner);

        $qrCodeService = new QrCodeService;
        $this->qrCodeSvg = $qrCodeService->generate($url);

        $imageData = $qrCodeService->withSize(512, 2)->overlayOnWhatsAppImage($url);
        $this->whatsappQrImage = 'data:image/png;base64,'.base64_encode($imageData);
    }

    #[Title('Registration Successful - CREDAI Glam Property Show 2026')]
    public function render()
    {
        return view('livewire.partner-success');
    }
}
