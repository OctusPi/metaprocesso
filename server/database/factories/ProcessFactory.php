<?php

namespace Database\Factories;

use App\Models\Dfd;
use App\Models\Process;
use App\Models\Unit;
use App\Models\User;
use App\Models\Organ;
use App\Models\Comission;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Process>
 */
class ProcessFactory extends Factory
{
    protected $model = Process::class;

    public function definition(): array
    {
        $dfds = Dfd::with('ordinator')->latest()->limit(1)->get();
        $comission = Comission::latest()->first();
        $faker = Faker::create('pt_BR');

        return [
            'status' => Process::S_ABERTO,
            'comission_id' => $comission->id,
            'dfds' => $dfds->toArray(),
            'year_pca' => date('Y'),
            'units' => Unit::latest()->limit(1)->get()->toArray(),
            'type' => $faker->randomElement(Process::list_types())['id'],
            'modality' => $faker->randomElement(Process::list_modalitys())['id'],
            'organ_id' => Organ::latest()->first()->id,
            'author_id' => User::latest()->first()->id,
            'protocol' => $faker->numerify('###-########-####'),
            'ip' => $faker->numerify('###.###.###.###'),
            'comission_address' => $faker->address(),
            'date_hour_ini' => date('d/m/Y H:i'),
            'description' => $faker->text(100),
            'ordinators' => collect($dfds->toArray())->pluck('ordinator_id')->toArray(),
            'comission_members' => $comission->comissionmembers->toArray(),
            'category' => $faker->randomElement(Process::list_categories())['id'],
            'dispute' => $faker->randomElement(Process::list_disputes())['id'],
            'benefit' => $faker->randomElement(Process::list_benefits())['id'],
            'acquisition' => $faker->randomElement(Process::list_acquisitions())['id'],
            'acquisition_type' => $faker->randomElement(Process::list_acquisition_types())['id'],
            'installment_justification' => $faker->text(200),
            'installment_type' => $faker->randomElement(Process::list_installments_types())['id'],
        ];
    }
}
