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
            'comission' => Comission::latest()->first()->id,
            'organ' => Organ::latest()->first()->id,
            'name' => fake()->name(),
            'description' => fake()->text(200)
        ];
    }
}
