<?php

namespace Database\Factories;

use App\Models\Comission;
use App\Models\ComissionMember;
use App\Models\Organ;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ComissionMember>
 */
class ComissionMemberFactory extends Factory
{
    protected $model = ComissionMember::class;

    public function definition(): array
    {
        $faker = Faker::create('pt_BR');

        return [
            'responsibility' => $faker->randomElement(ComissionMember::list_responsabilities())['id'],
            'status' => $faker->randomElement(ComissionMember::list_status())['id'],
            'comission_id' => Comission::latest()->first()->id,
            'organ_id' => Organ::latest()->first()->id,
            'unit_id' => Unit::latest()->first()->id,
            'start_term' => $faker->date('d/m/Y'),
            'name' => $faker->company(),
        ];
    }
}
