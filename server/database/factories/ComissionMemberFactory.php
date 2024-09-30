<?php

namespace Database\Factories;

use App\Models\Comission;
use App\Models\ComissionMember;
use App\Models\Organ;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ComissionMember>
 */
class ComissionMemberFactory extends Factory
{
    public function definition(): array
    {
        return [
            'responsibility' => fake()->randomElement(ComissionMember::list_responsabilities())['id'],
            'status' => fake()->randomElement(ComissionMember::list_status())['id'],
            'comission_id' => Comission::latest()->first()->id,
            'organ_id' => Organ::latest()->first()->id,
            'unit_id' => Unit::latest()->first()->id,
            'start_term' => fake()->date('d/m/Y'),
            'name' => fake()->company(),
        ];
    }
}
