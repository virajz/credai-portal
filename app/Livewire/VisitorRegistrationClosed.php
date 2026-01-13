<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.front')]
class VisitorRegistrationClosed extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('livewire.visitor-registration-closed');
    }
}
