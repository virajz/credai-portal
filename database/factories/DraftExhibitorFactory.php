<?php

namespace Database\Factories;

use App\Models\DraftExhibitor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DraftExhibitor>
 */
class DraftExhibitorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'resume_token' => DraftExhibitor::generateResumeToken(),
            'brand_name' => fake()->company(),
            'office_address' => fake()->address(),
            'city' => fake()->randomElement(['Surat', 'Navsari', 'Ahmedabad', 'Baroda', 'Others']),
            'contact_person_name' => fake()->name(),
            'phone_number' => fake()->numerify('##########'),
            'email' => fake()->email(),
            'website' => fake()->url(),
            'video_url' => fake()->url(),
            'social_media_links' => [
                'facebook' => fake()->url(),
                'linkedin' => fake()->url(),
                'instagram' => fake()->url(),
            ],
            'facia_name' => strtoupper(fake()->company()),
            'current_step' => fake()->numberBetween(1, 4),
            'completed_steps' => [],
            'is_completed' => false,
            'last_activity_at' => fake()->dateTimeBetween('-7 days', 'now'),
        ];
    }

    /**
     * Indicate that the draft is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_completed' => true,
            'current_step' => 4,
            'completed_steps' => [1, 2, 3, 4],
        ]);
    }

    /**
     * Indicate that the draft is incomplete.
     */
    public function incomplete(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_completed' => false,
        ]);
    }
}
