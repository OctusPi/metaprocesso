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
            'protocol'=> fake()->numerify('##########'),
            'ip' => fake()->numerify('###.###.###.###'),
            'organ' => Organ::inRandomOrder()->first()->id,
            'unit' => Unit::inRandomOrder()->first()->id,
            'demandant' => Demandant::inRandomOrder()->first()->id,
            'ordinator' => Ordinator::inRandomOrder()->first()->id,
            'comission' => Comission::inRandomOrder()->first()->id,
            'author' => User::inRandomOrder()->first()->id,
            'bonds' => false,
            'price_taking' => false,
            'date_ini' => fake()->date('d/m/Y'),
            'estimated_date'=>fake()->date('d/m/Y'),
            'description'=>fake()->text(100),
            'justification'=>fake()->text(100),
            'year_pca' => fake()->year(),
            'estimated_value' => fake()->randomNumber(5),
            'acquisition_type' => fake()->randomElement(Dfd::list_acquisitions())['id'],
            'suggested_hiring'=>fake()->randomElement(Dfd::list_hirings())['id'],
            'priority'=>fake()->randomElement(Dfd::list_priority())['id'],
            'status' => fake()->randomElement(Dfd::list_status())['id'],
        ];
    }
}
