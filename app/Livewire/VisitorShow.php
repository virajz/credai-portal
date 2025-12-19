<?php

namespace App\Livewire;

use App\Models\Visitor;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.front')]
class VisitorShow extends Component
{
    public Visitor $visitor;

    #[Title('Visitor Profile - CREDAI Glam Property Show 2026')]
    public function render()
    {
        return view('livewire.visitor-show');
    }
}
