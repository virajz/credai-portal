<?php

namespace App\Console\Commands;

use App\Models\Project;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateCommercialCategories extends Command
{
    protected $signature = 'projects:update-commercial-categories {--dry-run : Run without making changes}';

    protected $description = 'Rename "Commercial" to "Commercial Office" and create duplicates with "Commercial Shop / Showroom"';

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
            $this->newLine();
        }

        // Find all projects with 'Commercial' category
        $commercialProjects = Project::where('category', 'Commercial')->get();

        if ($commercialProjects->isEmpty()) {
            $this->info('✅ No projects with "Commercial" category found.');

            return self::SUCCESS;
        }

        $this->info("Found {$commercialProjects->count()} project(s) with 'Commercial' category:");
        $this->newLine();

        // Display the projects that will be affected
        foreach ($commercialProjects as $project) {
            $this->line("  • ID: {$project->id} - {$project->name} (Exhibitor: {$project->exhibitor->company->company_name})");
        }

        $this->newLine();

        if (! $isDryRun && ! $this->confirm('Do you want to proceed with the update?', true)) {
            $this->info('Operation cancelled.');

            return self::SUCCESS;
        }

        if (! $isDryRun) {
            DB::beginTransaction();

            try {
                $updatedCount = 0;
                $duplicatedCount = 0;

                foreach ($commercialProjects as $project) {
                    // Step 1: Rename to 'Commercial Office'
                    $project->update(['category' => 'Commercial Office']);
                    $updatedCount++;

                    // Step 2: Create duplicate with 'Commercial Shop / Showroom'
                    $duplicate = $project->replicate();
                    $duplicate->category = 'Commercial Shop / Showroom';
                    $duplicate->slug = null; // Will auto-generate a unique slug
                    $duplicate->save();
                    $duplicatedCount++;

                    $this->line("  ✓ Updated project #{$project->id} and created duplicate #{$duplicate->id}");
                }

                DB::commit();

                $this->newLine();
                $this->info("✅ Successfully updated {$updatedCount} project(s) to 'Commercial Office'");
                $this->info("✅ Successfully created {$duplicatedCount} duplicate project(s) with 'Commercial Shop / Showroom'");
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("❌ Error: {$e->getMessage()}");

                return self::FAILURE;
            }
        } else {
            $this->info("Would update {$commercialProjects->count()} project(s) to 'Commercial Office'");
            $this->info("Would create {$commercialProjects->count()} duplicate project(s) with 'Commercial Shop / Showroom'");
        }

        return self::SUCCESS;
    }
}
