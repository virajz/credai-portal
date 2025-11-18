<?php

namespace App\Livewire;

use App\Models\Exhibitor;
use Livewire\Attributes\Title;
use Livewire\Component;

class ExhibitorDetails extends Component
{
    public Exhibitor $exhibitor;

    public function mount(Exhibitor $exhibitor): void
    {
        $this->exhibitor = $exhibitor->load(['company', 'projects']);
    }

    #[Title('Exhibitor Details')]
    public function render()
    {
        return view('livewire.exhibitor-details');
    }
}
