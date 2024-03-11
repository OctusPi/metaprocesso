<?php

namespace Database\Factories;

use App\Models\Organ;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Program>
 */
class ProgramFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'organ_id' => fake()->numberBetween(1, Organ::all()->count()),
            'unit_id' =>  fake()->numberBetween(1, Unit::all()->count()),
            'law' => fake()->text(255),
            'description' => fake()->paragraph(),
            'status' => 1
        ];
    }
}
