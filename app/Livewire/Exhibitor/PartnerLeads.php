<?php

namespace App\Livewire\Exhibitor;

use App\Models\PartnerLead;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.exhibitor')]
class PartnerLeads extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function deleteLead(int $leadId): void
    {
        $company = auth('exhibitor')->user();

        $lead = PartnerLead::where('id', $leadId)
            ->where('company_id', $company->id)
            ->first();

        if ($lead) {
            $lead->delete();
            Flux::toast('Lead removed successfully!', variant: 'success');
        }
    }

    #[Title('Partner Leads - Exhibitor Portal')]
    public function render()
    {
        $company = auth('exhibitor')->user();

        $leads = $company->partnerLeads()
            ->with('partner')
            ->when($this->search, function ($query) {
                $query->whereHas('partner', function ($q) {
                    $q->where('first_name', 'ilike', "%{$this->search}%")
                        ->orWhere('last_name', 'ilike', "%{$this->search}%")
                        ->orWhere('phone', 'like', "%{$this->search}%")
                        ->orWhere('firm_name', 'ilike', "%{$this->search}%");
                });
            })
            ->latest()
            ->paginate(20);

        return view('livewire.exhibitor.partner-leads', [
            'leads' => $leads,
        ]);
    }
}
