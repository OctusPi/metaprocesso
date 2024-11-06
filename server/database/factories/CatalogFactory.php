<?php

namespace Database\Factories;

use App\Models\Catalog;
use App\Models\Comission;
use App\Models\Organ;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Catalog>
 */
class CatalogFactory extends Factory
{
    protected $model = Catalog::class;

    public function definition(): array
    {
        $faker = Faker::create('pt_BR');

        return [
            'comission_id' => Comission::latest()->first()->id,
            'organ_id' => Organ::latest()->first()->id,
            'name' => $faker->name(),
            'description' => $faker->text(200)
        ];
    }
}
