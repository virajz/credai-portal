<?php

namespace App\Livewire\Components;

use Livewire\Component;

class ExportButton extends Component
{
    public function exportAll(): void
    {
        $this->dispatch('export-visitors-all');
    }

    public function render()
    {
        return view('livewire.components.export-button');
    }
}
