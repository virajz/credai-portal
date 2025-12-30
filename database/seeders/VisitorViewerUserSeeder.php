<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class VisitorViewerUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'visitor@glam2026.com'],
            [
                'name' => 'Visitor Viewer',
                'password' => Hash::make('Visitor@2025'),
                'role' => 'visitor_viewer',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Visitor viewer user created/updated successfully.');
    }
}
