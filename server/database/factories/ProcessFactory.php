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
        $dfds = Dfd::with('ordinator')->latest()->limit(1)->get();
        $comission = Comission::latest()->first();

        return [
            'status' => Process::S_ABERTO,
            'comission_id' => $comission->id,
            'dfds' => $dfds->toArray(),
            'year_pca' => date('Y'),
            'units' => Unit::latest()->limit(1)->get()->toArray(),
            'type' => fake()->randomElement(Process::list_types())['id'],
            'modality' => fake()->randomElement(Process::list_modalitys())['id'],
            'organ_id' => Organ::latest()->first()->id,
            'author_id' => User::latest()->first()->id,
            'protocol' => fake()->numerify('###-########-####'),
            'ip' => fake()->numerify('###.###.###.###'),
            'comission_address' => fake()->address(),
            'date_hour_ini' => date('d/m/Y H:i'),
            'description' => fake()->text(100),
            'ordinators' => collect($dfds->toArray())->pluck('ordinator_id')->toArray(),
            'comission_members' => $comission->comissionmembers->toArray(),
            'acquisition' => fake()->randomElement(Process::list_acquisitions())['id'],
            'acquisition_type' => fake()->randomElement(Process::list_acquisition_types())['id'],
            'installment_justification' => fake()->text(200),
            'installment_type' => fake()->randomElement(Process::list_installments_types())['id'],
        ];
    }
}
