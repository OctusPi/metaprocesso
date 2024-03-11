<?php

namespace Database\Factories;

use App\Models\Organ;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PriceRegistrationDoc>
 */
class PriceRegistrationDocFactory extends Factory
{
    public function definition(): array
    {
        return [
            'cod' => fake()->text(20),
            'category' => fake()->numberBetween(1, 3),
            'obj' => fake()->text(),
            'description' => fake()->text(),
            'organ' => fake()->numberBetween(1, Organ::all()->count()),
            'unit' =>  fake()->numberBetween(1, Unit::all()->count()),
            'date_ini' => fake()->date(),
            'date_fin' => fake()->date(),
            'estimated_value' => fake()->randomFloat(2, 1, 10000),
            'approved_value' => fake()->randomFloat(2, 1, 10000),
            'supplier' => fake()->numberBetween(1, Supplier::all()->count()),
            'additive' => fake()->boolean(),
            'status' => fake()->numberBetween(1, 3)
        ];
    }
}
