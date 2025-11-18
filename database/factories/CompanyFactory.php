<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_name' => fake()->company(),
            'registered_number' => fake()->numerify('##########'),
            'main_person_name' => fake()->name(),
            'stall_type' => fake()->randomElement(['Corner', 'Island', 'Linear']),
            'stall_number' => fake()->bothify('?-###'),
            'stall_size' => fake()->randomElement(['3m x 3m', '6m x 3m', '50 sq ft', '100 sq ft']),
        ];
    }
}
