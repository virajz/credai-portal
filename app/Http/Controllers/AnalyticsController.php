<?php

namespace App\Http\Controllers;

use App\Models\Exhibitor;
use App\Models\Project;
use App\Services\Analytics;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class AnalyticsController extends Controller
{
    /**
     * Track and download exhibitor brochure
     */
    public function downloadExhibitorBrochure(Exhibitor $exhibitor): RedirectResponse
    {
        Analytics::trackBrochureDownload($exhibitor);

        return redirect(Storage::url($exhibitor->brochure_path));
    }

    /**
     * Track and download project brochure
     */
    public function downloadProjectBrochure(Project $project): RedirectResponse
    {
        Analytics::trackProjectBrochureDownload($project);

        return redirect(Storage::url($project->pdf_path));
    }

    /**
     * Track call to exhibitor
     */
    public function trackCallExhibitor(Exhibitor $exhibitor): RedirectResponse
    {
        Analytics::trackCall($exhibitor);

        return redirect('tel:'.$exhibitor->phone_number);
    }

    /**
     * Track call to project contact
     */
    public function trackCallProject(Project $project): RedirectResponse
    {
        Analytics::trackCall($project);

        return redirect('tel:'.$project->exhibitor->phone_number);
    }

    /**
     * Track website visit
     */
    public function trackWebsiteVisit(Exhibitor $exhibitor): RedirectResponse
    {
        if (! $exhibitor->website) {
            abort(404, 'Website not available');
        }

        Analytics::trackWebsiteVisit($exhibitor);

        return redirect($exhibitor->website);
    }
}
