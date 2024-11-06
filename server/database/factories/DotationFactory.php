<?php

namespace Database\Factories;

use App\Models\Dotation;
use App\Models\Organ;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dotation>
 */
class DotationFactory extends Factory
{
    protected $model = Dotation::class;

    public function definition(): array
    {
        $faker = Faker::create('pt_BR');

        return [
            'status' => $faker->randomElement(Dotation::list_status())['id'],
            'organ_id' => Organ::latest()->first()->id,
            'unit_id' => Unit::latest()->first()->id,
            'description' => $faker->text(200),
            'name' => $faker->company(),
            'law' => $faker->text(200),
        ];
    }
}
