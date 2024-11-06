<?php

namespace Database\Factories;

use App\Models\Organ;
use App\Models\Program;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Program>
 */
class ProgramFactory extends Factory
{
    protected $model = Program::class;

    public function definition(): array
    {
        $faker = Faker::create('pt_BR');

        return [
            'status' => Program::S_ACTIVE,
            'organ_id' => Organ::latest()->first()->id,
            'unit_id' => Unit::latest()->first()->id,
            'name' => $faker->company(),
            'law' => $faker->text(200),
            'description' => $faker->text(200),
        ];
    }
}
