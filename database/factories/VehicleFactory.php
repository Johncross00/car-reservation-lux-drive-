<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $brands = ['Toyota', 'Volkswagen', 'Ford', 'Renault', 'Peugeot', 'BMW', 'Mercedes-Benz', 'Audi'];
        $colors = ['Blanc', 'Noir', 'Gris', 'Rouge', 'Bleu', 'Vert', 'Argent'];

        return [
            'brand' => fake()->randomElement($brands),
            'model' => fake()->word().' '.fake()->numberBetween(100, 999),
            'plate_number' => strtoupper(fake()->bothify('??-###-??')),
            'color' => fake()->randomElement($colors),
            'year' => fake()->numberBetween(2015, 2024),
            'description' => fake()->optional()->sentence(),
            'is_available' => true,
        ];
    }
}
