<?php

namespace Database\Factories;

use App\Models\Contract;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ContractAdditive>
 */
class ContractAdditiveFactory extends Factory
{
    public function definition(): array
    {
        return [
            'contract' => fake()->numberBetween(1, Contract::all()->count()),
            'date_ini' => fake()->date(),
            'obj' => fake()->text(),
            'description' => fake()->text()
        ];
    }
}
