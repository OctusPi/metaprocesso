<?php

namespace Database\Factories;

use App\Models\Organ;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Unit>
 */
class UnitFactory extends Factory
{

    protected $model = Unit::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = Faker::create('pt_BR');

        return [
            'organ_id' => Organ::latest()->first()->id,
            'name' => $faker->company(),
            'cnpj' => $faker->numerify('##############'),
            'phone' => $faker->numerify('(##) #########'),
            'email' => $faker->email(),
            'address' => $faker->address()
        ];
    }
}
