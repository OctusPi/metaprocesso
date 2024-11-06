<?php

namespace Database\Factories;

use App\Models\Organ;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supplier>
 */
class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    public function definition(): array
    {
        $faker = Faker::create('pt_BR');

        return [
            'organ_id' => Organ::latest()->first()->id,
            'name' => $faker->company(),
            'cnpj' => $faker->numerify('##.###.###/####-##'),
            'agent' => $faker->name(),
            'cpf' => $faker->numerify('###.###.###-##'),
            'phone' => $faker->numerify('(##) #########'),
            'email' => $faker->email(),
            'address' => $faker->address(),
            'modality' => $faker->randomElement(Supplier::list_modalitys())['id'],
            'size' => $faker->randomElement(Supplier::list_sizes())['id'],
            'flag' => Supplier::FLAG_INTERN,
        ];
    }
}
