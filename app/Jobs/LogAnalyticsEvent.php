<?php

namespace App\Jobs;

use App\Models\AnalyticsEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Queue\Queueable;

class LogAnalyticsEvent implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $eventType,
        public ?Model $trackable = null,
        public ?int $visitorId = null,
        public ?array $metadata = null,
        public ?string $ipAddress = null,
        public ?string $userAgent = null,
        public ?string $sessionId = null,
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        AnalyticsEvent::create([
            'event_type' => $this->eventType,
            'trackable_type' => $this->trackable ? get_class($this->trackable) : null,
            'trackable_id' => $this->trackable?->id,
            'visitor_id' => $this->visitorId,
            'metadata' => $this->metadata,
            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
            'session_id' => $this->sessionId,
        ]);
    }
}
