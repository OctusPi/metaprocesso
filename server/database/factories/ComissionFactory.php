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
            'type' => fake()->randomElement(Comission::list_types())['id'],
            'status' => fake()->randomElement(Comission::list_status())['id'],
            'organ' => Organ::inRandomOrder()->first()->id,
            'unit' => Unit::inRandomOrder()->first()->id,
            'start_term' => fake()->date('d/m/Y'),
            'name' => fake()->company(),
        ];
    }
}
