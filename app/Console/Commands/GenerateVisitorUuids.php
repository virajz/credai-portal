<?php

namespace App\Console\Commands;

use App\Models\Visitor;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateVisitorUuids extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'visitors:generate-uuids';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate UUIDs for existing visitors that don\'t have one';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Generating UUIDs for visitors...');

        $visitors = Visitor::whereNull('uuid')->get();

        if ($visitors->isEmpty()) {
            $this->info('All visitors already have UUIDs.');

            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($visitors->count());
        $bar->start();

        foreach ($visitors as $visitor) {
            $visitor->uuid = (string) Str::uuid();
            $visitor->save();
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info("Successfully generated UUIDs for {$visitors->count()} visitors.");

        return self::SUCCESS;
    }
}
