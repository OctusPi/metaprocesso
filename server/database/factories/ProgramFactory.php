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
            'status' => Program::S_ACTIVE,
            'organ' => Organ::latest()->first()->id,
            'unit' => Unit::latest()->first()->id,
            'name' => fake()->company(),
            'law' => fake()->text(200),
            'description' => fake()->text(200),
        ];
    }
}
