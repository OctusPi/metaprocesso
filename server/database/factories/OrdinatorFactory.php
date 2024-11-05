<?php

namespace Database\Factories;

use App\Models\Ordinator;
use App\Models\Organ;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ordinator>
 */
class OrdinatorFactory extends Factory
{
    protected $model = Ordinator::class;

    public function definition(): array
    {
        $faker = Faker::create('pt_BR');

        return [
            'status' => $faker->randomElement(Ordinator::list_status())['id'],
            'organ_id' => Organ::latest()->first()->id,
            'unit_id' => Unit::latest()->first()->id,
            'cpf' => $faker->numerify('###.###.###-##'),
            'start_term' => $faker->date('d/m/Y'),
            'name' => $faker->name(),
        ];
    }
}
