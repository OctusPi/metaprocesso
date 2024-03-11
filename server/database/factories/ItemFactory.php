<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'cod' => fake()->text(50),
            'name' => fake()->word(),
            'description' => fake()->paragraph(),
            'unit' => fake()->word(),
            'volume' => fake()->text(50)
        ];
    }
}
