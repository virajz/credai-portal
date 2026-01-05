<?php

namespace App\Livewire;

use App\Models\AnalyticsEvent;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class CompanyAnalytics extends Component
{
    use WithPagination;

    public Company $company;

    public string $dateRange = '7'; // Default to last 7 days

    public string $eventTypeFilter = 'all';

    public function mount(Company $company): void
    {
        $this->company = $company->load('exhibitor.projects');
    }

    public function getAnalyticsSummaryProperty(): array
    {
        $query = $this->getBaseQuery();

        $totalEvents = $query->count();

        $eventsByType = $this->getBaseQuery()
            ->select('event_type', DB::raw('count(*) as count'))
            ->groupBy('event_type')
            ->pluck('count', 'event_type')
            ->toArray();

        return [
            'total_events' => $totalEvents,
            'profile_views' => $eventsByType['exhibitor_profile_view'] ?? 0,
            'project_views' => $eventsByType['project_view'] ?? 0,
            'brochure_downloads' => $eventsByType['company_brochure_download'] ?? 0,
            'project_brochure_downloads' => $eventsByType['project_brochure_download'] ?? 0,
            'calls' => $eventsByType['call_clicked'] ?? 0,
            'website_visits' => $eventsByType['website_visit_clicked'] ?? 0,
            'qr_scans' => $eventsByType['qr_scanned'] ?? 0,
        ];
    }

    protected function getBaseQuery()
    {
        $exhibitor = $this->company->exhibitor;

        if (! $exhibitor) {
            return AnalyticsEvent::query()->whereRaw('1 = 0'); // Return empty query
        }

        $query = AnalyticsEvent::query()
            ->where(function ($q) use ($exhibitor) {
                $q->where('trackable_type', \App\Models\Exhibitor::class)
                    ->where('trackable_id', $exhibitor->id)
                    ->orWhereIn('trackable_id', $exhibitor->projects->pluck('id'))
                    ->where('trackable_type', \App\Models\Project::class);
            });

        if ($this->dateRange !== 'all') {
            $query->where('created_at', '>=', now()->subDays((int) $this->dateRange));
        }

        if ($this->eventTypeFilter !== 'all') {
            $query->where('event_type', $this->eventTypeFilter);
        }

        return $query;
    }

    public function render()
    {
        $events = $this->getBaseQuery()
            ->with('trackable')
            ->latest()
            ->paginate(20);

        return view('livewire.company-analytics', [
            'events' => $events,
            'summary' => $this->analyticsSummary,
        ]);
    }
}
