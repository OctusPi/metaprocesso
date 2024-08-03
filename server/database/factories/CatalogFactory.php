<?php

namespace Database\Factories;

use App\Models\Comission;
use App\Models\Organ;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Catalog>
 */
class CatalogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'organ' => Organ::inRandomOrder()->first()->id,
            'comission' =>  Comission::inRandomOrder()->first()->id,
            'name' => fake()->name(),
            'description' => fake()->text(100)
        ];
    }
}
