<?php

namespace App\Livewire;

use App\Models\EntryExitLog;
use Livewire\Attributes\Computed;
use Livewire\Component;

class TotalVisitorsEnteredWidget extends Component
{
    #[Computed]
    public function chartData(): array
    {
        $endDate = now();
        $startDate = now()->subDays(6);

        $entries = EntryExitLog::query()
            ->whereBetween('entry_time', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->selectRaw('DATE(entry_time) as date, COUNT(DISTINCT visitor_id) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $data = [];
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateKey = $date->format('Y-m-d');
            $data[] = [
                'date' => $dateKey,
                'visitors' => $entries->get($dateKey)?->count ?? 0,
            ];
        }

        return $data;
    }

    public function render()
    {
        return view('livewire.total-visitors-entered-widget');
    }
}
