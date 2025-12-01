<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AnalyticsEvent extends Model
{
    protected $fillable = [
        'event_type',
        'trackable_type',
        'trackable_id',
        'visitor_id',
        'metadata',
        'ip_address',
        'user_agent',
        'session_id',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    /**
     * Get the trackable model (Exhibitor, Project, etc.)
     */
    public function trackable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the visitor associated with this event
     */
    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class);
    }
}
