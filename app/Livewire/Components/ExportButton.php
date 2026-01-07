<?php

namespace App\Livewire\Components;

use App\Models\EntryExitLog;
use Livewire\Component;

class ExportButton extends Component
{
    public bool $showDateModal = false;

    public ?string $selectedDate = null;

    public int $visitorCount = 0;

    public function openDateModal(): void
    {
        $this->showDateModal = true;
        $this->selectedDate = null;
        $this->visitorCount = 0;
    }

    public function selectDate(string $date): void
    {
        $this->selectedDate = $date;
        $this->visitorCount = $this->getVisitorCount($date);
    }

    protected function getVisitorCount(string $date): int
    {
        $query = EntryExitLog::whereNotNull('visitor_id');

        if ($date !== 'all') {
            $query->whereDate('entry_time', $date);
        }

        return $query->distinct('visitor_id')->count('visitor_id');
    }

    public function exportDateWise(): void
    {
        if (! $this->selectedDate) {
            return;
        }

        $this->dispatch('export-visitors-date-wise', date: $this->selectedDate);
    }

    public function closeModal(): void
    {
        $this->showDateModal = false;
        $this->selectedDate = null;
        $this->visitorCount = 0;
    }

    public function exportAll(): void
    {
        $this->dispatch('export-visitors-all');
    }

    public function render()
    {
        return view('livewire.components.export-button');
    }
}
