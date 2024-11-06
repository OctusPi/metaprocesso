<?php

namespace Database\Factories;

use App\Models\Organ;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organ>
 */
class OrganFactory extends Factory
{
    protected $model = Organ::class;

    public function definition(): array
    {
        $faker = Faker::create('pt_BR');
        return [
            'status' => $faker->randomElement(Organ::list_status())['id'],
            'name' => $faker->company(),
            'cnpj' => $faker->numerify('##.###.###/####-##'),
            'phone' => $faker->numerify('(##) #####-####'),
            'email' => $faker->companyEmail(),
            'address' => $faker->address(),
            'postalcode' => $faker->numerify('#####-###'),
            'postalcity' => $faker->city(),
        ];
    }
}
