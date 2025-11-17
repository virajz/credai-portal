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
            'gst_number' => fake()->optional()->regexify('[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}'),
            'pan_number' => fake()->optional()->regexify('[A-Z]{5}[0-9]{4}[A-Z]{1}'),
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
                    'youtube' => 'https://youtube.com/@company',
                ],
                null,
            ]),
            'facia_name' => strtoupper(fake()->company()),
            'additional_details' => fake()->optional()->paragraph(),
        ];
    }
}
