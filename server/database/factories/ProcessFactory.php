<?php

namespace Database\Factories;

use App\Models\Dfd;
use App\Models\Process;
use App\Models\Unit;
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
        $dfds = Dfd::with('ordinator')->limit(3)->get();
        $comission = Comission::inRandomOrder()->first();

        return [
            'type' => fake()->randomElement(Process::list_types())['id'],
            'modality' => fake()->randomElement(Process::list_modalitys())['id'],
            'status' => fake()->randomElement(Process::list_status())['id'],
            'comission' => $comission->id,
            'organ' => Organ::inRandomOrder()->first()->id,
            'author' => User::inRandomOrder()->first()->id,
            'protocol'=> fake()->numerify('###-########-####'),
            'ip' => fake()->numerify('###.###.###.###'),
            'comission_address' => fake()->address(),
            'date_hour_ini' => date('d/m/Y H:i'),
            'description'=>fake()->text(100),
            'year_pca' => '2024',
            'units' => Unit::limit(3)->get()->toArray(),
            'dfds' => $dfds->toArray(),
            'ordinators' => collect($dfds->toArray())->pluck('ordinator')->toArray(),
            'comission_members' => $comission->comissionmembers->toArray(),
        ];
    }
}
