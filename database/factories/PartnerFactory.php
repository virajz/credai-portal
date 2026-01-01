<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Partner>
 */
class PartnerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'firm_name' => fake()->optional()->company(),
            'email' => fake()->optional()->email(),
            'phone' => fake()->numerify('##########'),
            'areas' => fake()->randomElements([
                'Athwa - Vesu',
                'Pal - Adajan - Rander',
                'Katargam',
                'Olpad',
                'Puna Kumbhaiya',
                'Varachha',
                'Udhna - Sachin',
                'Dindoli',
                'Kamrej',
                'Saroli',
                'Old City',
                'Outer City Area',
            ], fake()->numberBetween(1, 4)),
            'property_types' => fake()->randomElements([
                'Apartment',
                'Bungalows',
                'Plot',
                'Farm House',
                'Shops',
                'Showrooms',
                'Office Spaces',
            ], fake()->numberBetween(1, 3)),
        ];
    }
}
