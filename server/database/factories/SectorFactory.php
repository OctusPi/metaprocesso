<?php

namespace Database\Factories;

use App\Models\Organ;
use App\Models\Sector;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sector>
 */
class SectorFactory extends Factory
{

    protected $model = Sector::class;

    public function definition(): array
    {
        $faker = Faker::create('pt_BR');

        return [
            'organ_id' => Organ::latest()->first()->id,
            'unit_id' =>  Unit::latest()->first()->id,
            'name' => $faker->company()
        ];
    }
}
