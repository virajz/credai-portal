<?php

namespace App\Livewire\Exhibitor;

use App\Models\AnalyticsEvent;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.exhibitor')]
class Dashboard extends Component
{
    public function getAnalyticsSummaryProperty(): array
    {
        $company = auth('exhibitor')->user();
        $exhibitor = $company->exhibitor;

        if (! $exhibitor) {
            return [
                'total_events' => 0,
                'profile_views' => 0,
                'project_views' => 0,
                'brochure_downloads' => 0,
                'calls' => 0,
                'website_visits' => 0,
                'total_leads' => 0,
            ];
        }

        $eventsByType = AnalyticsEvent::query()
            ->where(function ($q) use ($exhibitor) {
                $q->where('trackable_type', \App\Models\Exhibitor::class)
                    ->where('trackable_id', $exhibitor->id)
                    ->orWhereIn('trackable_id', $exhibitor->projects->pluck('id'))
                    ->where('trackable_type', \App\Models\Project::class);
            })
            ->select('event_type', DB::raw('count(*) as count'))
            ->groupBy('event_type')
            ->pluck('count', 'event_type')
            ->toArray();

        return [
            'total_events' => array_sum($eventsByType),
            'profile_views' => $eventsByType['exhibitor_profile_view'] ?? 0,
            'project_views' => $eventsByType['project_view'] ?? 0,
            'brochure_downloads' => ($eventsByType['company_brochure_download'] ?? 0) + ($eventsByType['project_brochure_download'] ?? 0),
            'calls' => $eventsByType['call_clicked'] ?? 0,
            'website_visits' => $eventsByType['website_visit_clicked'] ?? 0,
            'total_leads' => $company->leads()->count(),
        ];
    }

    #[Title('Dashboard - Exhibitor Portal')]
    public function render()
    {
        return view('livewire.exhibitor.dashboard', [
            'summary' => $this->analyticsSummary,
        ]);
    }
}
