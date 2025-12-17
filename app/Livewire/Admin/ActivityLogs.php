<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ActivityLogs extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public ?string $filterAction = null;

    #[Url]
    public ?int $filterUserId = null;

    public function mount(): void
    {
        $this->authorize('admin-access');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'filterAction', 'filterUserId']);
        $this->resetPage();
    }

    public function hasActiveFilters(): bool
    {
        return ! empty($this->search) || $this->filterAction !== null || $this->filterUserId !== null;
    }

    #[Title('Activity Logs')]
    public function render()
    {
        $logs = ActivityLog::query()
            ->with('user')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('action', 'ilike', "%{$this->search}%")
                        ->orWhere('description', 'ilike', "%{$this->search}%")
                        ->orWhere('ip_address', 'ilike', "%{$this->search}%");
                });
            })
            ->when($this->filterAction, function ($query) {
                $query->where('action', $this->filterAction);
            })
            ->when($this->filterUserId, function ($query) {
                $query->where('user_id', $this->filterUserId);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $actions = ActivityLog::query()
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return view('livewire.admin.activity-logs', [
            'logs' => $logs,
            'actions' => $actions,
        ]);
    }
}
