<?php

namespace App\Livewire\Exhibitor;

use App\Models\ExhibitorLead;
use App\Models\Partner;
use App\Models\PartnerLead;
use App\Models\Visitor;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.exhibitor')]
class ScanVisitor extends Component
{
    public ?Visitor $scannedVisitor = null;

    public ?Partner $scannedPartner = null;

    public string $leadType = ''; // 'visitor' or 'partner'

    public string $notes = '';

    public string $visitorUuid = '';

    public function searchVisitor(): void
    {
        $this->validate([
            'visitorUuid' => 'required|uuid',
        ]);

        // Auto-detect if UUID belongs to visitor or partner
        $visitor = Visitor::where('uuid', $this->visitorUuid)->first();
        $partner = Partner::where('uuid', $this->visitorUuid)->first();

        if ($visitor) {
            $this->scannedVisitor = $visitor;
            $this->scannedPartner = null;
            $this->leadType = 'visitor';
            $this->notes = '';
        } elseif ($partner) {
            $this->scannedPartner = $partner;
            $this->scannedVisitor = null;
            $this->leadType = 'partner';
            $this->notes = '';
        } else {
            $this->addError('visitorUuid', 'Invalid QR code. No visitor or partner found with this code.');
        }
    }

    public function addLead(): void
    {
        if (! $this->scannedVisitor && ! $this->scannedPartner) {
            return;
        }

        $company = auth('exhibitor')->user();

        if ($this->leadType === 'visitor' && $this->scannedVisitor) {
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

            Flux::toast('Visitor lead added successfully!', variant: 'success');
        } elseif ($this->leadType === 'partner' && $this->scannedPartner) {
            $existingLead = PartnerLead::where('company_id', $company->id)
                ->where('partner_id', $this->scannedPartner->id)
                ->first();

            if ($existingLead) {
                Flux::toast('This partner has already been added as a lead.', variant: 'warning');

                return;
            }

            PartnerLead::create([
                'company_id' => $company->id,
                'partner_id' => $this->scannedPartner->id,
                'notes' => $this->notes,
            ]);

            Flux::toast('Partner lead added successfully!', variant: 'success');
        }

        $this->reset(['scannedVisitor', 'scannedPartner', 'leadType', 'notes', 'visitorUuid']);
    }

    public function reset(...$properties): void
    {
        $this->scannedVisitor = null;
        $this->scannedPartner = null;
        $this->leadType = '';
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
