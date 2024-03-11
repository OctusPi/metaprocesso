<?php

namespace Database\Factories;

use App\Models\Organ;
use App\Models\Sector;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Stockout>
 */
class StockoutFactory extends Factory
{
    public function definition(): array
    {
        return [
            'organ_id' => fake()->numberBetween(1, Organ::all()->count()),
            'unit_id' => fake()->numberBetween(1, Unit::all()->count()),
            'sector_id' => fake()->numberBetween(1, Sector::all()->count()),
            'cod' => fake()->text(20),
            'date_ini' => fake()->date(),
            'description' => fake()->text(),
            'issuer' => fake()->name(),
            'requester' => fake()->name()
        ];
    }
}
