<?php

namespace App\Livewire;

use App\Models\EntryExitLog;
use App\Models\HourlyWinner;
use Livewire\Component;

class LuckyDraw extends Component
{
    public ?array $winner = null;

    public bool $isDrawing = false;

    public string $winnerName = '';

    public string $winnerPhone = '';

    public function drawWinner(): void
    {
        $oneHourAgo = now()->subHour();

        $winnerIds = HourlyWinner::pluck('visitor_id')->toArray();

        $eligibleEntry = EntryExitLog::with('visitor')
            ->where('entry_time', '>=', $oneHourAgo)
            ->whereNotNull('visitor_id')
            ->whereNotIn('visitor_id', $winnerIds)
            ->inRandomOrder()
            ->first();

        if (! $eligibleEntry || ! $eligibleEntry->visitor) {
            $this->dispatch('no-eligible-entries');

            return;
        }

        HourlyWinner::create([
            'visitor_id' => $eligibleEntry->visitor_id,
            'entry_exit_log_id' => $eligibleEntry->id,
            'visitor_name' => $eligibleEntry->visitor->name,
            'visitor_phone' => $eligibleEntry->visitor->phone,
            'drawn_at' => now(),
        ]);

        $this->isDrawing = true;
        $this->winner = null;
        $this->winnerName = $eligibleEntry->visitor->name;
        $this->winnerPhone = $eligibleEntry->visitor->phone;

        $this->dispatch('start-animation');
    }

    public function finishAnimation(): void
    {
        $this->winner = [
            'name' => $this->winnerName,
            'phone' => $this->winnerPhone,
        ];
        $this->isDrawing = false;
    }

    public function resetDraw(): void
    {
        $this->winner = null;
        $this->isDrawing = false;
    }

    public function render()
    {
        return view('livewire.lucky-draw')->layout('components.layouts.front', [
            'title' => 'Lucky Draw - '.config('app.name'),
        ]);
    }
}
