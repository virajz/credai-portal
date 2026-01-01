<?php

namespace App\Console\Commands;

use App\Models\Project;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RevertCommercialCategories extends Command
{
    protected $signature = 'projects:revert-commercial-categories {--dry-run : Run without making changes}';

    protected $description = 'Revert Commercial Office/Shop categories back to single Commercial category with unit types';

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
            $this->newLine();
        }

        // Find all projects with 'Commercial Office' or 'Commercial Shop / Showroom' category
        $commercialProjects = Project::whereIn('category', ['Commercial Office', 'Commercial Shop / Showroom'])
            ->orderBy('exhibitor_id')
            ->orderBy('name')
            ->get();

        if ($commercialProjects->isEmpty()) {
            $this->info('✅ No projects with "Commercial Office" or "Commercial Shop / Showroom" category found.');

            return self::SUCCESS;
        }

        $this->info("Found {$commercialProjects->count()} project(s) with commercial sub-categories:");
        $this->newLine();

        if (! $isDryRun) {
            DB::beginTransaction();

            try {
                $merged = 0;
                $updated = 0;
                $deleted = 0;
                $processedIds = [];

                foreach ($commercialProjects as $project) {
                    // Skip if already processed
                    if (in_array($project->id, $processedIds)) {
                        continue;
                    }

                    // Determine the unit type based on current category
                    $unitType = match ($project->category) {
                        'Commercial Office' => 'commercial-office',
                        'Commercial Shop / Showroom' => 'commercial-shop',
                        default => 'commercial'
                    };

                    // Update units array to include the type
                    $units = is_array($project->units) ? $project->units : (json_decode($project->units, true) ?? []);
                    foreach ($units as &$unit) {
                        $unit['type'] = $unitType;
                    }

                    // Look for potential duplicate (same exhibitor, name, area, and other details except category)
                    $duplicate = Project::where('id', '!=', $project->id)
                        ->where('exhibitor_id', $project->exhibitor_id)
                        ->where('name', $project->name)
                        ->where('area', $project->area)
                        ->where('sq_ft', $project->sq_ft)
                        ->where('budget_range', $project->budget_range)
                        ->where('handover_date', $project->handover_date)
                        ->where('status', $project->status)
                        ->where('usp', $project->usp)
                        ->whereIn('category', ['Commercial Office', 'Commercial Shop / Showroom'])
                        ->whereNotIn('id', $processedIds)
                        ->first();

                    if ($duplicate) {
                        // Merge: Combine units from both projects
                        $duplicateUnits = is_array($duplicate->units) ? $duplicate->units : (json_decode($duplicate->units, true) ?? []);
                        $duplicateUnitType = match ($duplicate->category) {
                            'Commercial Office' => 'commercial-office',
                            'Commercial Shop / Showroom' => 'commercial-shop',
                            default => 'commercial'
                        };

                        foreach ($duplicateUnits as &$unit) {
                            $unit['type'] = $duplicateUnitType;
                        }

                        // Combine units
                        $combinedUnits = array_merge($units, $duplicateUnits);

                        // Update the first project with combined units
                        $project->update([
                            'category' => 'Commercial',
                            'units' => $combinedUnits,
                        ]);

                        // Delete the duplicate
                        $duplicate->delete();

                        $this->line("  ✓ Merged projects #{$project->id} and #{$duplicate->id} into #{$project->id}: {$project->name}");
                        $merged++;
                        $deleted++;
                        $processedIds[] = $project->id;
                        $processedIds[] = $duplicate->id;
                    } else {
                        // No duplicate, just update this project
                        $project->update([
                            'category' => 'Commercial',
                            'units' => $units,
                        ]);

                        $this->line("  ✓ Updated project #{$project->id}: {$project->name} (Category: {$project->category})");
                        $updated++;
                        $processedIds[] = $project->id;
                    }
                }

                DB::commit();

                $this->newLine();
                $this->info("✅ Successfully converted {$commercialProjects->count()} project(s):");
                $this->info("   • {$merged} project pair(s) merged");
                $this->info("   • {$updated} project(s) updated without merging");
                $this->info("   • {$deleted} duplicate project(s) deleted");
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("❌ Error: {$e->getMessage()}");

                return self::FAILURE;
            }
        } else {
            // Dry run - show what would happen
            $wouldMerge = 0;
            $wouldUpdate = 0;
            $processedIds = [];

            foreach ($commercialProjects as $project) {
                if (in_array($project->id, $processedIds)) {
                    continue;
                }

                $duplicate = Project::where('id', '!=', $project->id)
                    ->where('exhibitor_id', $project->exhibitor_id)
                    ->where('name', $project->name)
                    ->where('area', $project->area)
                    ->where('sq_ft', $project->sq_ft)
                    ->where('budget_range', $project->budget_range)
                    ->where('handover_date', $project->handover_date)
                    ->where('status', $project->status)
                    ->where('usp', $project->usp)
                    ->whereIn('category', ['Commercial Office', 'Commercial Shop / Showroom'])
                    ->whereNotIn('id', $processedIds)
                    ->first();

                if ($duplicate) {
                    $this->line("  • Would merge: #{$project->id} ({$project->category}) + #{$duplicate->id} ({$duplicate->category}) → {$project->name}");
                    $wouldMerge++;
                    $processedIds[] = $project->id;
                    $processedIds[] = $duplicate->id;
                } else {
                    $this->line("  • Would update: #{$project->id} ({$project->category}) → Commercial: {$project->name}");
                    $wouldUpdate++;
                    $processedIds[] = $project->id;
                }
            }

            $this->newLine();
            $this->info('Summary of what would happen:');
            $this->info("  • {$wouldMerge} project pair(s) would be merged");
            $this->info("  • {$wouldUpdate} project(s) would be updated without merging");
        }

        return self::SUCCESS;
    }
}
