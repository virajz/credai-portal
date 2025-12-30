<?php

namespace App\Livewire;

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
    }

    #[Title('Registration Successful - CREDAI Glam Property Show 2026')]
    public function render()
    {
        return view('livewire.visitor-success');
    }
}
