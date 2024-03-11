<?php

namespace Database\Factories;

use App\Models\Organ;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dfd>
 */
class DfdFactory extends Factory
{
    public function definition(): array
    {
        return [
            'cod' => fake()->text(20),
            'date_ini' => fake()->date(),
            'category' => 1,
            'obj' => fake()->text(),
            'description' => fake()->text(),
            'organ' => fake()->numberBetween(1, Organ::all()->count()),
            'unit' => fake()->numberBetween(1, Unit::all()->count()),
        ];
    }
}
