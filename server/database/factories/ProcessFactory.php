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
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Process>
 */
class ProcessFactory extends Factory
{
    public function definition(): array
    {
        return [
            'protocol'=> (string)fake()->randomNumber(5),
            'ip' => fake()->numerify('########'),
            'date_hour_ini' => date('d/m/Y H:i:s'),
            'year_pca' => '2024',
            'type' => fake()->numberBetween(1,3),
            'modality' => fake()->numberBetween(1,3),
            'organ' => Organ::inRandomOrder()->first()->id,
            'units' => [],
            'ordinators' => [],
            'comission' => Comission::inRandomOrder()->first()->id,
            'comission_members' => [],
            'comission_address' => fake()->address(),
            'description'=>fake()->text(100),
            'status' => 1,
            'dfds' => [],
            'author' => User::inRandomOrder()->first()->id
        ];
    }
}
