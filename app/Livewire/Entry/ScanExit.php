<?php

namespace App\Livewire\Entry;

use App\Models\EntryExitLog;
use App\Models\Partner;
use App\Models\Visitor;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.public')]
class ScanExit extends Component
{
    public ?Visitor $scannedVisitor = null;

    public ?Partner $scannedPartner = null;

    public string $personType = ''; // 'visitor' or 'partner'

    public string $uuid = '';

    public function searchPerson(): void
    {
        $this->validate([
            'uuid' => 'required|uuid',
        ]);

        // Auto-detect if UUID belongs to visitor or partner
        $visitor = Visitor::where('uuid', $this->uuid)->first();
        $partner = Partner::where('uuid', $this->uuid)->first();

        if ($visitor) {
            $this->scannedVisitor = $visitor;
            $this->scannedPartner = null;
            $this->personType = 'visitor';
        } elseif ($partner) {
            $this->scannedPartner = $partner;
            $this->scannedVisitor = null;
            $this->personType = 'partner';
        } else {
            $this->addError('uuid', 'Invalid QR code. No visitor or partner found with this code.');
        }
    }

    public function recordExit(): void
    {
        if (! $this->scannedVisitor && ! $this->scannedPartner) {
            return;
        }

        if ($this->personType === 'visitor' && $this->scannedVisitor) {
            $entryLog = EntryExitLog::where('visitor_id', $this->scannedVisitor->id)
                ->whereDate('entry_time', today())
                ->whereNull('exit_time')
                ->latest('entry_time')
                ->first();

            $name = $this->scannedVisitor->name;
        } elseif ($this->personType === 'partner' && $this->scannedPartner) {
            $entryLog = EntryExitLog::where('partner_id', $this->scannedPartner->id)
                ->whereDate('entry_time', today())
                ->whereNull('exit_time')
                ->latest('entry_time')
                ->first();

            $name = $this->scannedPartner->first_name.' '.$this->scannedPartner->last_name;
        } else {
            return;
        }

        if ($entryLog) {
            $entryLog->update(['exit_time' => now()]);
            Flux::toast("Exit recorded successfully for {$name}!", variant: 'success');
        } else {
            Flux::toast("No entry record found for {$name} today.", variant: 'warning');
        }

        $this->reset(['scannedVisitor', 'scannedPartner', 'personType', 'uuid']);

        $this->dispatch('restart-camera');
    }

    public function reset(...$properties): void
    {
        $this->scannedVisitor = null;
        $this->scannedPartner = null;
        $this->personType = '';
        $this->uuid = '';
        $this->resetErrorBag();
    }

    #[Title('Exit Scanner')]
    public function render()
    {
        return view('livewire.entry.scan-exit');
    }
}
