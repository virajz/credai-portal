<?php

namespace App\Livewire;

use App\Models\AnalyticsEvent;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ExhibitorAnalyticsSummary extends Component
{
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

    public function render()
    {
        return view('livewire.exhibitor-analytics-summary');
    }
}
