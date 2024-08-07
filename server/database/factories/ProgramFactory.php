<?php

namespace Database\Factories;

use App\Models\Organ;
use App\Models\Program;
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
            'status' => fake()->randomElement(Program::list_status())['id'],
            'organ' => Organ::inRandomOrder()->first()->id,
            'unit' =>  Unit::inRandomOrder()->first()->id,
            'name' => fake()->company(),
            'law' => fake()->text(255),
            'description' => fake()->paragraph(),
        ];
    }
}
