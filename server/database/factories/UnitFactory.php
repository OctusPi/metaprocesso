<?php

namespace Database\Factories;

use App\Models\Organ;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Unit>
 */
class UnitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organ' => random_int(1, Organ::get()->count()),
            'name' => fake()->company(),
            'cnpj' => fake()->numerify('##############'),
            'phone' => fake()->numerify('(##) #########'),
            'email' => fake()->email(),
            'address' => fake()->address()
        ];
    }
}
