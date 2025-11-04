<?php

namespace App\Livewire;

use App\Models\DraftExhibitor;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class DraftExhibitorsList extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filter = 'incomplete';

    public ?int $draftToDelete = null;

    /**
     * Reset pagination when search changes
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when filter changes
     */
    public function updatedFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Copy resume link to clipboard (dispatches event to JS)
     */
    public function copyResumeLink(int $draftId): void
    {
        $draft = DraftExhibitor::findOrFail($draftId);
        $this->dispatch('copy-to-clipboard', url: $draft->resume_url);
    }

    /**
     * Confirm delete a draft
     */
    public function confirmDelete(int $draftId): void
    {
        $this->draftToDelete = $draftId;
        $this->modal('delete-draft')->show();
    }

    /**
     * Delete a draft
     */
    public function deleteDraft(): void
    {
        if ($this->draftToDelete) {
            $draft = DraftExhibitor::findOrFail($this->draftToDelete);
            $contactName = $draft->contact_person_name ?: 'Draft submission';

            $draft->delete();

            $this->draftToDelete = null;
            $this->modal('delete-draft')->close();

            Flux::toast(
                heading: 'Draft deleted',
                text: "{$contactName} has been removed successfully.",
                variant: 'success'
            );
        }
    }

    /**
     * Cancel delete operation
     */
    public function cancelDelete(): void
    {
        $this->draftToDelete = null;
        $this->modal('delete-draft')->close();
    }

    #[Title('Draft Exhibitor Submissions')]
    public function render()
    {
        $query = DraftExhibitor::query()
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('brand_name', 'like', "%{$this->search}%")
                        ->orWhere('contact_person_name', 'like', "%{$this->search}%")
                        ->orWhere('phone_number', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filter === 'incomplete', fn ($q) => $q->where('is_completed', false))
            ->when($this->filter === 'completed', fn ($q) => $q->where('is_completed', true))
            ->orderBy('last_activity_at', 'desc');

        $drafts = $query->paginate(15);

        // Calculate statistics
        $stats = [
            'total' => DraftExhibitor::count(),
            'incomplete' => DraftExhibitor::where('is_completed', false)->count(),
            'completed' => DraftExhibitor::where('is_completed', true)->count(),
            'active_today' => DraftExhibitor::where('is_completed', false)
                ->whereDate('last_activity_at', today())
                ->count(),
        ];

        return view('livewire.draft-exhibitors-list', [
            'drafts' => $drafts,
            'stats' => $stats,
        ]);
    }
}
