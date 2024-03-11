<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organ>
 */
class OrganFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'cnpj' => fake()->numerify('##############'),
            'phone' => fake()->numerify('(##) #########'),
            'email' => fake()->email(),
            'address' => fake()->address(),
            'postalcity' => 1,
            'postalcode' => fake()->postcode(),
            'status' => 1
        ];
    }
}
