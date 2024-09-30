<?php

namespace Database\Factories;

use App\Models\Ordinator;
use App\Models\Organ;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ordinator>
 */
class OrdinatorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'status' => fake()->randomElement(Ordinator::list_status())['id'],
            'organ_id' => Organ::latest()->first()->id,
            'unit_id' => Unit::latest()->first()->id,
            'cpf' => fake()->numerify('###.###.###-##'),
            'start_term' => fake()->date('d/m/Y'),
            'name' => fake()->name(),
        ];
    }
}
