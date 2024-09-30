<?php

namespace Database\Factories;

use App\Models\Comission;
use App\Models\Organ;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comission>
 */
class ComissionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'status' => Comission::STATUS_ACTIVE,
            'type' => fake()->randomElement(Comission::list_types())['id'],
            'organ_id' => Organ::latest()->first()->id,
            'unit_id' => Unit::latest()->first()->id,
            'start_term' => fake()->date('d/m/Y'),
            'name' => fake()->company(),
        ];
    }
}
