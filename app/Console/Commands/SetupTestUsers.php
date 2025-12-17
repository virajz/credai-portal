<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SetupTestUsers extends Command
{
    protected $signature = 'setup:test-users';

    protected $description = 'Set up test users (viraj@glam2026.com and abdul@glam2026.com)';

    public function handle(): int
    {
        $this->info('Setting up test users...');
        $this->newLine();

        $this->info('Removing existing test users...');
        $removedCount = User::whereNotIn('email', ['viraj@glam2026.com', 'abdul@glam2026.com'])->delete();
        $this->info("Removed {$removedCount} test user(s).");
        $this->newLine();

        $virajPassword = bin2hex(random_bytes(8)).'@V1raj';
        $viraj = User::updateOrCreate(
            ['email' => 'viraj@glam2026.com'],
            [
                'name' => 'Viraj Admin',
                'password' => Hash::make($virajPassword),
                'email_verified_at' => now(),
            ]
        );
        $this->components->info('Created/Updated: viraj@glam2026.com');
        $this->line("  Password: {$virajPassword}");
        $this->newLine();

        $abdulPassword = bin2hex(random_bytes(8)).'@A8dul';
        $abdul = User::updateOrCreate(
            ['email' => 'abdul@glam2026.com'],
            [
                'name' => 'Abdul User',
                'password' => Hash::make($abdulPassword),
                'email_verified_at' => now(),
            ]
        );
        $this->components->info('Created/Updated: abdul@glam2026.com');
        $this->line("  Password: {$abdulPassword}");
        $this->newLine();

        $this->components->success('Setup complete!');
        $this->newLine();

        $this->table(
            ['Account', 'Email', 'Password'],
            [
                ['Admin Account', 'viraj@glam2026.com', $virajPassword],
                ['User Account', 'abdul@glam2026.com', $abdulPassword],
            ]
        );

        return self::SUCCESS;
    }
}
