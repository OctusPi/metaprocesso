<?php

namespace Database\Factories;

use App\Models\Comission;
use App\Models\Organ;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ComissionMember>
 */
class ComissionMemberFactory extends Factory
{
    public function definition(): array
    {
        return [
            'organ' => Organ::inRandomOrder()->first()->id,
            'unit' =>  Unit::inRandomOrder()->first()->id,
            'comission' =>  Comission::inRandomOrder()->first()->id,
            'name' => fake()->word(),
            'responsibility' => fake()->numberBetween(1, 2),
            'start_term' => fake()->date('d/m/Y'),
            'status' => 1
        ];
    }
}
