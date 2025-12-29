<?php

namespace App\Console\Commands;

use App\Models\Company;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateCompanyUuids extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'companies:generate-uuids {--force : Force update all companies, even those with existing UUIDs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate UUIDs for companies that do not have one';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Generating UUIDs for companies...');

        $query = Company::query();

        if (! $this->option('force')) {
            $query->whereNull('uuid');
        }

        $companies = $query->get();

        if ($companies->isEmpty()) {
            $this->info('No companies need UUID generation.');

            return self::SUCCESS;
        }

        $this->info("Found {$companies->count()} companies to update.");

        $bar = $this->output->createProgressBar($companies->count());
        $bar->start();

        $updated = 0;

        foreach ($companies as $company) {
            $company->update([
                'uuid' => Str::uuid()->toString(),
            ]);

            $updated++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Successfully generated UUIDs for {$updated} companies.");

        return self::SUCCESS;
    }
}
