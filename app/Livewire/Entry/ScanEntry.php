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
class ScanEntry extends Component
{
    public ?Visitor $scannedVisitor = null;

    public ?Partner $scannedPartner = null;

    public string $personType = ''; // 'visitor' or 'partner'

    public string $uuid = '';

    public bool $alreadyEntered = false;

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

            // Check if already entered today
            $this->checkTodayEntry('visitor_id', $visitor->id);
        } elseif ($partner) {
            $this->scannedPartner = $partner;
            $this->scannedVisitor = null;
            $this->personType = 'partner';

            // Check if already entered today
            $this->checkTodayEntry('partner_id', $partner->id);
        } else {
            $this->addError('uuid', 'Invalid QR code. No visitor or partner found with this code.');
        }
    }

    protected function checkTodayEntry(string $field, int $id): void
    {
        $todayEntry = EntryExitLog::where($field, $id)
            ->whereDate('entry_time', today())
            ->latest('entry_time')
            ->first();

        $this->alreadyEntered = $todayEntry !== null;
    }

    public function recordEntry(): void
    {
        if (! $this->scannedVisitor && ! $this->scannedPartner) {
            return;
        }

        if ($this->personType === 'visitor' && $this->scannedVisitor) {
            $field = 'visitor_id';
            $id = $this->scannedVisitor->id;
            $name = $this->scannedVisitor->name;
        } elseif ($this->personType === 'partner' && $this->scannedPartner) {
            $field = 'partner_id';
            $id = $this->scannedPartner->id;
            $name = $this->scannedPartner->first_name.' '.$this->scannedPartner->last_name;
        } else {
            return;
        }

        // Find or create today's entry
        $todayEntry = EntryExitLog::where($field, $id)
            ->whereDate('entry_time', today())
            ->first();

        if ($todayEntry) {
            // Update existing entry time
            $todayEntry->update(['entry_time' => now()]);
            Flux::toast("Entry time updated for {$name}", variant: 'warning');
        } else {
            // Create new entry
            EntryExitLog::create([
                $field => $id,
                'entry_time' => now(),
            ]);
            Flux::toast("Entry recorded successfully for {$name}!", variant: 'success');
        }

        $this->reset(['scannedVisitor', 'scannedPartner', 'personType', 'uuid', 'alreadyEntered']);

        $this->dispatch('restart-camera');
    }

    public function reset(...$properties): void
    {
        $this->scannedVisitor = null;
        $this->scannedPartner = null;
        $this->personType = '';
        $this->uuid = '';
        $this->alreadyEntered = false;
        $this->resetErrorBag();
    }

    #[Title('Entry Scanner')]
    public function render()
    {
        return view('livewire.entry.scan-entry');
    }
}
