<?php

namespace Database\Factories;

use App\Models\Organ;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comission>
 */
class ComissionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'organ' => Organ::inRandomOrder()->first()->id,
            'unit' =>  Unit::inRandomOrder()->first()->id,
            'name' => fake()->text(100),
            'type' => fake()->numberBetween(1, 3),
            'start_term' => fake()->date('d/m/Y'),
            'status' => 1
        ];
    }
}
