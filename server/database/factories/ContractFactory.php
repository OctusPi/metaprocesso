<?php

namespace Database\Factories;

use App\Models\Organ;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contract>
 */
class ContractFactory extends Factory
{
    public function definition(): array
    {
        return [
            'cod' => fake()->text(),
            'category' => fake()->numberBetween(1, 5),
            'obj' => fake()->text(),
            'description' => fake()->text(),
            'organ_id' => fake()->numberBetween(1, Organ::all()->count()),
            'unit_id' => fake()->numberBetween(1, Unit::all()->count()),
            'date_ini' => "12/12/2023",
            'date_fin' => "12/12/2024",
            'estimated_value' => fake()->randomFloat(2, 1000, 10000),
            'approved_value' => fake()->randomFloat(2, 1000, 10000),
            'supplier_id' => fake()->numberBetween(1, Supplier::all()->count()),
            'additive' => fake()->boolean(),
            'status' => fake()->numberBetween(1, 3)
        ];
    }
}
