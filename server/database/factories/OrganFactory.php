<?php

namespace Database\Factories;

use App\Models\Organ;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organ>
 */
class OrganFactory extends Factory
{
    public function definition(): array
    {
        return [
            'status' => fake()->randomElement(Organ::list_status())['id'],
            'name' => fake()->company(),
            'cnpj' => fake()->numerify('##.###.###/####-##'),
            'phone' => fake()->numerify('(##) #########'),
            'email' => fake()->email(),
            'address' => fake()->address(),
            'postalcode' => fake()->numerify('#####-###'),
            'postalcity' => fake()->numberBetween(0, 3),
        ];
    }
}
