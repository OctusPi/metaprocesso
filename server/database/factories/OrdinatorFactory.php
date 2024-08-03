<?php

namespace Database\Factories;

use App\Models\Organ;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ordinator>
 */
class OrdinatorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'organ' => Organ::inRandomOrder()->first()->id,
            'unit' =>  Unit::inRandomOrder()->first()->id,
            'name' => fake()->name(),
            'cpf' => fake()->numerify('###########'),
            'start_term' => fake()->date('d/m/Y'),
            'status' => 1
        ];
    }
}
