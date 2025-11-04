<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Exhibitor>
 */
class ExhibitorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'brand_name' => fake()->company(),
            'office_address' => fake()->address(),
            'city' => fake()->city(),
            'contact_person_name' => fake()->name(),
            'phone_number' => fake()->phoneNumber(),
            'email' => fake()->optional()->safeEmail(),
            'website' => fake()->optional()->url(),
            'logo_path' => null,
            'brochure_path' => null,
            'photos' => null,
            'video_url' => fake()->optional()->url(),
            'social_media_links' => fake()->optional()->randomElement([
                [
                    'facebook' => 'https://facebook.com/company',
                    'linkedin' => 'https://linkedin.com/company/company',
                    'instagram' => 'https://instagram.com/company',
                ],
                null,
            ]),
            'facia_name' => strtoupper(fake()->company()),
        ];
    }
}
