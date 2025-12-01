<?php

namespace App\Services;

use App\Jobs\LogAnalyticsEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class Analytics
{
    /**
     * Log an analytics event
     */
    public static function track(
        string $eventType,
        ?Model $trackable = null,
        ?int $visitorId = null,
        ?array $metadata = null
    ): void {
        LogAnalyticsEvent::dispatch(
            eventType: $eventType,
            trackable: $trackable,
            visitorId: $visitorId,
            metadata: $metadata,
            ipAddress: Request::ip(),
            userAgent: Request::userAgent(),
            sessionId: session()->getId(),
        );
    }

    /**
     * Track exhibitor profile view (unique per 24 hours per session)
     */
    public static function trackExhibitorView(Model $exhibitor, ?int $visitorId = null): void
    {
        $sessionId = session()->getId();
        $ipAddress = Request::ip();

        // Check if this session/IP has viewed this exhibitor in the last 24 hours
        $recentView = \App\Models\AnalyticsEvent::where('event_type', 'exhibitor_profile_view')
            ->where('trackable_type', get_class($exhibitor))
            ->where('trackable_id', $exhibitor->id)
            ->where(function ($query) use ($sessionId, $ipAddress) {
                $query->where('session_id', $sessionId)
                    ->orWhere('ip_address', $ipAddress);
            })
            ->where('created_at', '>=', now()->subHours(24))
            ->exists();

        // Only track if no recent view exists
        if (! $recentView) {
            self::track('exhibitor_profile_view', $exhibitor, $visitorId);
        }
    }

    /**
     * Track project view
     */
    public static function trackProjectView(Model $project, ?int $visitorId = null): void
    {
        self::track('project_view', $project, $visitorId);
    }

    /**
     * Track company brochure download
     */
    public static function trackBrochureDownload(Model $exhibitor, ?int $visitorId = null): void
    {
        self::track('company_brochure_download', $exhibitor, $visitorId);
    }

    /**
     * Track project brochure download
     */
    public static function trackProjectBrochureDownload(Model $project, ?int $visitorId = null): void
    {
        self::track('project_brochure_download', $project, $visitorId);
    }

    /**
     * Track call action
     */
    public static function trackCall(Model $trackable, ?int $visitorId = null, ?array $metadata = null): void
    {
        self::track('call_clicked', $trackable, $visitorId, $metadata);
    }

    /**
     * Track website visit
     */
    public static function trackWebsiteVisit(Model $trackable, ?int $visitorId = null): void
    {
        self::track('website_visit_clicked', $trackable, $visitorId);
    }
}
