<?php

namespace App\Livewire\Exhibitor;

use App\Models\ExhibitorLead;
use App\Models\Visitor;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.exhibitor')]
class ScanVisitor extends Component
{
    public ?Visitor $scannedVisitor = null;

    public string $notes = '';

    public string $visitorUuid = '';

    public function searchVisitor(): void
    {
        $this->validate([
            'visitorUuid' => 'required|uuid|exists:visitors,uuid',
        ]);

        $this->scannedVisitor = Visitor::where('uuid', $this->visitorUuid)->first();
        $this->notes = '';
    }

    public function addLead(): void
    {
        if (! $this->scannedVisitor) {
            return;
        }

        $company = auth('exhibitor')->user();

        $existingLead = ExhibitorLead::where('company_id', $company->id)
            ->where('visitor_id', $this->scannedVisitor->id)
            ->first();

        if ($existingLead) {
            Flux::toast('This visitor has already been added as a lead.', variant: 'warning');

            return;
        }

        ExhibitorLead::create([
            'company_id' => $company->id,
            'visitor_id' => $this->scannedVisitor->id,
            'notes' => $this->notes,
        ]);

        Flux::toast('Lead added successfully!', variant: 'success');
        $this->reset(['scannedVisitor', 'notes', 'visitorUuid']);
    }

    public function reset(...$properties): void
    {
        $this->scannedVisitor = null;
        $this->notes = '';
        $this->visitorUuid = '';
        $this->resetErrorBag();
    }

    #[Title('Scan Visitor - Exhibitor Portal')]
    public function render()
    {
        return view('livewire.exhibitor.scan-visitor');
    }
}
