<?php

namespace App\Console\Commands;

use App\Models\Exhibitor;
use App\Models\Project;
use Illuminate\Console\Command;

class BackfillSlugs extends Command
{
    protected $signature = 'app:backfill-slugs';

    protected $description = 'Backfill slugs for exhibitors and projects that don\'t have one';

    public function handle(): int
    {
        $this->info('Backfilling slugs...');

        $exhibitorCount = 0;
        Exhibitor::whereNull('slug')->orWhere('slug', '')->each(function (Exhibitor $exhibitor) use (&$exhibitorCount) {
            $exhibitor->slug = $exhibitor->generateUniqueSlug();
            $exhibitor->saveQuietly();
            $exhibitorCount++;
        });
        $this->info("Updated {$exhibitorCount} exhibitors.");

        $projectCount = 0;
        Project::whereNull('slug')->orWhere('slug', '')->each(function (Project $project) use (&$projectCount) {
            $project->slug = $project->generateUniqueSlug();
            $project->saveQuietly();
            $projectCount++;
        });
        $this->info("Updated {$projectCount} projects.");

        $this->info('Done!');

        return self::SUCCESS;
    }
}
