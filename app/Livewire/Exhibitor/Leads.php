<?php

namespace App\Livewire\Exhibitor;

use App\Models\ExhibitorLead;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.exhibitor')]
class Leads extends Component
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

        $lead = ExhibitorLead::where('id', $leadId)
            ->where('company_id', $company->id)
            ->first();

        if ($lead) {
            $lead->delete();
            Flux::toast('Lead removed successfully!', variant: 'success');
        }
    }

    #[Title('My Leads - Exhibitor Portal')]
    public function render()
    {
        $company = auth('exhibitor')->user();

        $leads = $company->leads()
            ->with('visitor')
            ->when($this->search, function ($query) {
                $query->whereHas('visitor', function ($q) {
                    $q->where('name', 'ilike', "%{$this->search}%")
                        ->orWhere('phone', 'like', "%{$this->search}%")
                        ->orWhere('company_name', 'ilike', "%{$this->search}%");
                });
            })
            ->latest()
            ->paginate(20);

        return view('livewire.exhibitor.leads', [
            'leads' => $leads,
        ]);
    }
}
