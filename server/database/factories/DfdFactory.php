<?php

namespace Database\Factories;


use App\Models\Dfd;
use App\Models\Unit;
use App\Models\User;
use App\Models\Organ;
use App\Models\Comission;
use App\Models\Demandant;
use App\Models\Ordinator;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dfd>
 */
class DfdFactory extends Factory
{
    protected $model = Dfd::class;

    public function definition(): array
    {
        $faker = Faker::create('pt_BR');

        return [
            'status' => Dfd::STATUS_RASCUNHO,
            'protocol' => $faker->numerify('####-#######'),
            'ip' => $faker->numerify('###.###.###.###'),
            'organ_id' => Organ::latest()->first()->id,
            'unit_id' => Unit::latest()->first()->id,
            'demandant_id' => Demandant::latest()->first()->id,
            'ordinator_id' => Ordinator::latest()->first()->id,
            'comission_id' => Comission::latest()->first()->id,
            'author_id' => User::latest()->first()->id,
            'bonds' => false,
            'price_taking' => false,
            'date_ini' => date('d/m/Y'),
            'estimated_date' => date('d/m/Y'),
            'description' => $faker->text(200),
            'justification' => $faker->text(200),
            'year_pca' => date('Y'),
            'estimated_value' => $faker->randomNumber(5),
            'acquisition_type' => $faker->randomElement(Dfd::list_acquisitions())['id'],
            'suggested_hiring' => $faker->randomElement(Dfd::list_hirings())['id'],
            'priority' => $faker->randomElement(Dfd::list_priority())['id'],
        ];
    }
}
