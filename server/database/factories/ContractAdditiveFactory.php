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
            'contract_id' => fake()->numberBetween(1, Contract::all()->count()),
            'date_ini' => "12/12/2023",
            'obj' => fake()->text(),
            'description' => fake()->text()
        ];
    }
}
