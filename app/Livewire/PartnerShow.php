<?php

namespace App\Livewire;

use App\Models\Partner;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.front')]
class PartnerShow extends Component
{
    public Partner $partner;

    #[Title('Partner Profile - CREDAI Glam Property Show 2026')]
    public function render()
    {
        return view('livewire.partner-show');
    }
}
