<?php

namespace Database\Factories;

use App\Models\Demandant;
use App\Models\Organ;
use App\Models\Sector;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Demandant>
 */
class DemandantFactory extends Factory
{
    public function definition(): array
    {
        return [
            'status' => fake()->randomElement(Demandant::list_status())['id'],
            'sector' => Sector::latest()->first()->id,
            'organ' => Organ::latest()->first()->id,
            'unit' => Unit::latest()->first()->id,
            'cpf' => fake()->numerify('###.###.###-##'),
            'start_term' => fake()->date('d/m/Y'),
            'name' => fake()->name(),
        ];
    }
}
