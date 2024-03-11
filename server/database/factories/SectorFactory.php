<?php

namespace Database\Factories;

use App\Models\Organ;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sector>
 */
class SectorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'organ_id' => fake()->numberBetween(1, Organ::all()->count()),
            'unit_id' => fake()->numberBetween(1, Unit::all()->count()),
            'name' => fake()->company(),
            'cnpj' => fake()->numerify('##############'),
            'phone' => fake()->numerify('(##) #########'),
            'email' => fake()->email(),
            'address' => fake()->address()
        ];
    }
}
