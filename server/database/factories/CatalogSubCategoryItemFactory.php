<?php

namespace Database\Factories;


use App\Models\Organ;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CatalogSubCategoryItem>
 */
class CatalogSubCategoryItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'organ' => Organ::latest()->first()->id,
            'name' => fake()->name(),
        ];
    }
}
