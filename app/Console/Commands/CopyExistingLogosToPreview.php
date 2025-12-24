<?php

namespace App\Console\Commands;

use App\Models\Exhibitor;
use Illuminate\Console\Command;

class CopyExistingLogosToPreview extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logos:copy-to-preview';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy existing JPG/PNG logos to preview_logo column';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting to copy existing JPG/PNG logos to preview_logo column...');

        $exhibitors = Exhibitor::whereNotNull('logo_path')
            ->whereNull('preview_logo')
            ->get();

        if ($exhibitors->isEmpty()) {
            $this->info('No exhibitors found with logos that need to be copied.');

            return self::SUCCESS;
        }

        $this->info("Found {$exhibitors->count()} exhibitors with logos.");

        $copiedCount = 0;
        $skippedCount = 0;

        foreach ($exhibitors as $exhibitor) {
            $logoPath = $exhibitor->logo_path;
            $extension = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));

            if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
                $exhibitor->update([
                    'preview_logo' => $logoPath,
                ]);
                $copiedCount++;
                $this->line("✓ Copied logo for exhibitor ID {$exhibitor->id}: {$logoPath}");
            } else {
                $skippedCount++;
                $this->line("⊗ Skipped exhibitor ID {$exhibitor->id}: {$logoPath} (extension: {$extension})");
            }
        }

        $this->newLine();
        $this->info('Migration complete!');
        $this->info("Copied: {$copiedCount}");
        $this->info("Skipped: {$skippedCount}");

        return self::SUCCESS;
    }
}
