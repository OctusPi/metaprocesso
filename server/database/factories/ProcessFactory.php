<?php

namespace Database\Factories;

use App\Models\Process;
use App\Models\User;
use App\Models\Organ;
use App\Models\Comission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Process>
 */
class ProcessFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(Process::list_types())['id'],
            'modality' => fake()->randomElement(Process::list_modalitys())['id'],
            'status' => fake()->randomElement(Process::list_status())['id'],
            'organ' => Organ::inRandomOrder()->first()->id,
            'comission' => Comission::inRandomOrder()->first()->id,
            'author' => User::inRandomOrder()->first()->id,
            'protocol'=> fake()->numerify('###-########-####'),
            'ip' => fake()->numerify('###.###.###.###'),
            'comission_address' => fake()->address(),
            'date_hour_ini' => date('d/m/Y H:i'),
            'description'=>fake()->text(100),
            'year_pca' => '2024',
            'units' => [],
            'ordinators' => [],
            'comission_members' => [],
            'dfds' => [],
        ];
    }
}
