<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Visitor>
 */
class VisitorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $interests = fake()->randomElements(['Residential', 'Commercial', 'Plotting'], fake()->numberBetween(1, 3));
        $residentialTypes = in_array('Residential', $interests)
            ? fake()->randomElements(['2 BHK', '3 BHK', '4 BHK', '5 BHK', 'Others'], fake()->numberBetween(1, 3))
            : [];
        $commercialTypes = in_array('Commercial', $interests)
            ? fake()->randomElements(['Showroom', 'Shops', 'Offices', 'Others'], fake()->numberBetween(1, 2))
            : [];
        $plottingTypes = in_array('Plotting', $interests)
            ? fake()->randomElements(['Industrial', 'Residential'], fake()->numberBetween(1, 2))
            : [];

        return [
            'name' => fake()->name(),
            'phone' => fake()->numerify('##########'),
            'email' => fake()->optional()->email(),
            'company_name' => fake()->optional()->company(),
            'photo_path' => null,
            'interests' => $interests,
            'residential_types' => $residentialTypes,
            'commercial_types' => $commercialTypes,
            'plotting_types' => $plottingTypes,
            'planning_to_buy' => fake()->randomElement(['Within 3 months', 'Within 6 months', 'Within a year']),
            'areas' => fake()->randomElements([
                'Athwa - Vesu',
                'Pal - Adajan - Rander',
                'Katargam',
                'Varachha',
                'Udhna - Sachin',
                'Dindoli',
                'Kamrej',
                'Saroli',
                'Within City',
                'Outer City',
                'Puna Kumbhaiya',
            ], fake()->numberBetween(1, 4)),
        ];
    }
}
