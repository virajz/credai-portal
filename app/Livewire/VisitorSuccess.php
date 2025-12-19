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

    public function mount(Visitor $visitor): void
    {
        $url = route('visitor.show', $visitor);

        $qrCodeService = new QrCodeService;
        $this->qrCodeSvg = $qrCodeService->generate($url);
    }

    #[Title('Registration Successful - CREDAI Glam Property Show 2026')]
    public function render()
    {
        return view('livewire.visitor-success');
    }
}
