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

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dfd>
 */
class DfdFactory extends Factory
{
    public function definition(): array
    {
        return [
            'status' => Dfd::STATUS_RASCUNHO,
            'protocol' => fake()->numerify('####-#######'),
            'ip' => fake()->numerify('###.###.###.###'),
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
            'description' => fake()->text(200),
            'justification' => fake()->text(200),
            'year_pca' => date('Y'),
            'estimated_value' => fake()->randomNumber(5),
            'acquisition_type' => fake()->randomElement(Dfd::list_acquisitions())['id'],
            'suggested_hiring' => fake()->randomElement(Dfd::list_hirings())['id'],
            'priority' => fake()->randomElement(Dfd::list_priority())['id'],
        ];
    }
}
