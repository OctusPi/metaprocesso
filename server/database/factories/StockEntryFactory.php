<?php

namespace Database\Factories;

use App\Models\Contract;
use App\Models\PurchaseOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StockEntry>
 */
class StockEntryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'date_ini' => fake()->date(),
            'invoice' => fake()->text(50),
            'danfe' => fake()->text(),
            'purchaseorder' => fake()->numberBetween(1, PurchaseOrder::all()->count()),
            'contract' =>  fake()->numberBetween(1, Contract::all()->count()),
            'quantity' => fake()->numberBetween(1, 100),
            'current_value' => fake()->randomFloat(2, 10, 1000)
        ];
    }
}
