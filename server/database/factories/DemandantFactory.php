<?php

namespace Database\Factories;

use App\Models\Demandant;
use App\Models\Organ;
use App\Models\Sector;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Demandant>
 */
class DemandantFactory extends Factory
{
    protected $model = Demandant::class;

    public function definition(): array
    {
        $faker = Faker::create('pt_BR');

        return [
            'status' => $faker->randomElement(Demandant::list_status())['id'],
            'sector_id' => Sector::latest()->first()->id,
            'organ_id' => Organ::latest()->first()->id,
            'unit_id' => Unit::latest()->first()->id,
            'cpf' => $faker->numerify('###.###.###-##'),
            'start_term' => $faker->date('d/m/Y'),
            'name' => $faker->name(),
        ];
    }
}
