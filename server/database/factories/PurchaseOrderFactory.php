<?php

namespace Database\Factories;

use App\Models\Contract;
use App\Models\Organ;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchaseOrder>
 */
class PurchaseOrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'cod' => fake()->text(20),
            'date_ini' => fake()->date(),
            'organ' => fake()->numberBetween(1, Organ::all()->count()),
            'unit' =>  fake()->numberBetween(1, Unit::all()->count()),
            'contract' => fake()->numberBetween(1, Contract::all()->count()),
            'status' => fake()->numberBetween(1, 3)
        ];
    }
}
