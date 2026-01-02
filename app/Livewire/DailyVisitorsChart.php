<?php

namespace App\Livewire;

use App\Models\Visitor;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Component;

class DailyVisitorsChart extends Component
{
    #[Computed]
    public function chartData(): array
    {
        $endDate = now();
        $startDate = now()->subDays(6);

        $visitors = Visitor::query()
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $data = [];
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateKey = $date->format('Y-m-d');
            $data[] = [
                'date' => $dateKey,
                'visitors' => $visitors->get($dateKey)?->count ?? 0,
            ];
        }

        return $data;
    }

    public function render()
    {
        return view('livewire.daily-visitors-chart');
    }
}
