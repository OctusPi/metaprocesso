<?php

namespace Database\Factories;


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
            'protocol'=> (string)fake()->randomNumber(5),
            'ip' => fake()->numerify('########'),
            'organ' => Organ::inRandomOrder()->first()->id,
            'unit' => Unit::inRandomOrder()->first()->id,
            'demandant' => Demandant::inRandomOrder()->first()->id,
            'ordinator' => Ordinator::inRandomOrder()->first()->id,
            'comission' => Comission::inRandomOrder()->first()->id,
            'date_ini' => fake()->date('d/m/Y'),
            'year_pca' => '2024',
            'acquisition_type' => fake()->numberBetween(1,5),
            'suggested_hiring'=>fake()->numberBetween(1, 4),
            'description'=>fake()->text(100),
            'justification'=>fake()->text(100),
            'estimated_value' => fake()->randomNumber(5),
            'estimated_date'=>fake()->date('d/m/Y'),
            'priority'=>fake()->numberBetween(1,3),
            'status' => 1,
            'author' => User::inRandomOrder()->first()->id,
        ];
    }
}
