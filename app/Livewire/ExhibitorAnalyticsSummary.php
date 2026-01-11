<?php

namespace App\Livewire;

use App\Models\AnalyticsEvent;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class ExhibitorAnalyticsSummary extends Component
{
    use WithPagination;

    public bool $showModal = false;

    public string $selectedMetric = '';

    public string $selectedMetricLabel = '';

    #[Computed]
    public function summary(): array
    {
        $eventsByType = AnalyticsEvent::query()
            ->select('event_type', DB::raw('count(*) as count'))
            ->groupBy('event_type')
            ->pluck('count', 'event_type')
            ->toArray();

        return [
            'profile_views' => $eventsByType['exhibitor_profile_view'] ?? 0,
            'project_views' => $eventsByType['project_view'] ?? 0,
            'brochure_downloads' => $eventsByType['company_brochure_download'] ?? 0,
            'project_brochure_downloads' => $eventsByType['project_brochure_download'] ?? 0,
            'calls' => $eventsByType['call_clicked'] ?? 0,
            'website_visits' => $eventsByType['website_visit_clicked'] ?? 0,
            'qr_scans' => $eventsByType['qr_scanned'] ?? 0,
        ];
    }

    public function showDetails(string $metric, string $label): void
    {
        $this->selectedMetric = $metric;
        $this->selectedMetricLabel = $label;
        $this->showModal = true;
        $this->resetPage();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->selectedMetric = '';
        $this->selectedMetricLabel = '';
    }

    #[Computed]
    public function detailData()
    {
        if (! $this->showModal || ! $this->selectedMetric) {
            return null;
        }

        $eventTypes = $this->getEventTypesForMetric($this->selectedMetric);

        $query = Company::query()
            ->join('exhibitors', 'companies.id', '=', 'exhibitors.company_id');

        // Profile views are the only metric tracked on exhibitors
        // All other metrics (project views, downloads, calls, website visits, qr scans) are tracked on projects
        if ($this->selectedMetric === 'profile_views') {
            // Events tracked on the exhibitor
            $query->leftJoin('analytics_events', function ($join) use ($eventTypes) {
                $join->on('analytics_events.trackable_id', '=', 'exhibitors.id')
                    ->where('analytics_events.trackable_type', '=', \App\Models\Exhibitor::class)
                    ->whereIn('analytics_events.event_type', $eventTypes);
            });
        } else {
            // Events tracked on projects (and optionally exhibitors for downloads)
            $query->leftJoin('projects', 'exhibitors.id', '=', 'projects.exhibitor_id')
                ->leftJoin('analytics_events', function ($join) use ($eventTypes) {
                    $join->where(function ($q) use ($eventTypes) {
                        // Include events on projects
                        $q->where(function ($subQ) use ($eventTypes) {
                            $subQ->on('analytics_events.trackable_id', '=', 'projects.id')
                                ->where('analytics_events.trackable_type', '=', \App\Models\Project::class)
                                ->whereIn('analytics_events.event_type', $eventTypes);
                        });

                        // For downloads, also include company brochure downloads on exhibitors
                        if ($this->selectedMetric === 'downloads') {
                            $q->orWhere(function ($subQ) {
                                $subQ->on('analytics_events.trackable_id', '=', 'exhibitors.id')
                                    ->where('analytics_events.trackable_type', '=', \App\Models\Exhibitor::class)
                                    ->where('analytics_events.event_type', '=', 'company_brochure_download');
                            });
                        }
                    });
                });
        }

        return $query->select('companies.*', DB::raw('COUNT(analytics_events.id) as event_count'))
            ->groupBy('companies.id')
            ->havingRaw('COUNT(analytics_events.id) > 0')
            ->orderByDesc('event_count')
            ->paginate(10);
    }

    protected function getEventTypesForMetric(string $metric): array
    {
        return match ($metric) {
            'profile_views' => ['exhibitor_profile_view'],
            'project_views' => ['project_view'],
            'downloads' => ['company_brochure_download', 'project_brochure_download'],
            'calls' => ['call_clicked'],
            'website_visits' => ['website_visit_clicked'],
            'qr_scans' => ['qr_scanned'],
            default => [],
        };
    }

    public function render()
    {
        return view('livewire.exhibitor-analytics-summary');
    }
}
