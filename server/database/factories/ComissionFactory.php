<?php

namespace Database\Factories;

use App\Models\Comission;
use App\Models\Organ;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comission>
 */
class ComissionFactory extends Factory
{
    protected $model = Comission::class;

    public function definition(): array
    {
        $faker = Faker::create('pt_BR');

        return [
            'status' => Comission::STATUS_ACTIVE,
            'type' => $faker->randomElement(Comission::list_types())['id'],
            'organ_id' => Organ::latest()->first()->id,
            'unit_id' => Unit::latest()->first()->id,
            'start_term' => $faker->date('d/m/Y'),
            'name' => $faker->company(),
        ];
    }
}
