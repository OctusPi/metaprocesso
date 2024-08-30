<?php

namespace Database\Factories;

use App\Models\Organ;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supplier>
 */
class SupplierFactory extends Factory
{
    public function definition(): array
    {
        return [
            'organ' => Organ::inRandomOrder()->first()->id,
            'name' => fake()->company(),
            'cnpj' => fake()->numerify('##.###.###/####-##'),
            'agent' => fake()->name(),
            'cpf' => fake()->numerify('###.###.###-##'),
            'phone' => fake()->numerify('(##) #########'),
            'email' => fake()->email(),
            'address' => fake()->address(),
            'modality' => fake()->randomElement(Supplier::list_modalitys())['id'],
            'size' => fake()->randomElement(Supplier::list_sizes())['id'],
        ];
    }
}
