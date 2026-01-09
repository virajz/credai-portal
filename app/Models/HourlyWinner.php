<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HourlyWinner extends Model
{
    protected $fillable = [
        'visitor_id',
        'entry_exit_log_id',
        'visitor_name',
        'visitor_phone',
        'drawn_at',
        'is_awarded',
        'awarded_at',
    ];

    protected function casts(): array
    {
        return [
            'drawn_at' => 'datetime',
            'is_awarded' => 'boolean',
            'awarded_at' => 'datetime',
        ];
    }

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class);
    }

    public function entryExitLog(): BelongsTo
    {
        return $this->belongsTo(EntryExitLog::class);
    }
}
