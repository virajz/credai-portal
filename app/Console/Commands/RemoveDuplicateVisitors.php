<?php

namespace App\Console\Commands;

use App\Models\Visitor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RemoveDuplicateVisitors extends Command
{
    protected $signature = 'visitors:remove-duplicates {--dry-run : Show what would be removed without actually deleting}';

    protected $description = 'Remove duplicate visitor entries based on phone number, keeping the earliest registration';

    public function handle(): int
    {
        $this->info('Searching for duplicate phone numbers...');

        $duplicates = DB::table('visitors')
            ->select('phone', DB::raw('COUNT(*) as total'))
            ->groupBy('phone')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        if ($duplicates->isEmpty()) {
            $this->info('No duplicate phone numbers found.');

            return self::SUCCESS;
        }

        $this->warn("Found {$duplicates->count()} phone numbers with duplicates:");

        $totalDuplicates = 0;
        $recordsToDelete = [];

        foreach ($duplicates as $duplicate) {
            $visitors = Visitor::where('phone', $duplicate->phone)
                ->orderBy('created_at', 'asc')
                ->get();

            $keeper = $visitors->first();
            $duplicatesToRemove = $visitors->skip(1);

            $totalDuplicates += $duplicatesToRemove->count();

            $this->line("\nPhone: {$duplicate->phone}");
            $this->line("  Keeping: ID {$keeper->id} - {$keeper->name} (registered {$keeper->created_at})");

            foreach ($duplicatesToRemove as $dup) {
                $this->line("  Removing: ID {$dup->id} - {$dup->name} (registered {$dup->created_at})");
                $recordsToDelete[] = $dup->id;
            }
        }

        $this->newLine();
        $this->warn("Total duplicate records to remove: {$totalDuplicates}");

        if ($this->option('dry-run')) {
            $this->info('Dry run mode - no records were deleted.');

            return self::SUCCESS;
        }

        if (! $this->confirm('Do you want to proceed with deletion?', true)) {
            $this->info('Operation cancelled.');

            return self::SUCCESS;
        }

        $deleted = Visitor::whereIn('id', $recordsToDelete)->delete();

        $this->info("Successfully removed {$deleted} duplicate visitor records.");

        return self::SUCCESS;
    }
}
