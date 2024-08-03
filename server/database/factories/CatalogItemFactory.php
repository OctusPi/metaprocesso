<?php

namespace Database\Factories;


use App\Models\Organ;
use App\Models\Catalog;
use App\Models\CatalogSubCategoryItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CatalogItem>
 */
class CatalogItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'organ' => Organ::inRandomOrder()->first()->id,
            'catalog' => Catalog::inRandomOrder()->first()->id,
            'code' => fake()->word(),
            'name' => fake()->word(),
            'description' => fake()->text(100),
            'und'=>fake()->word(),
            'volume'=>fake()->word(),
            'origin'=>fake()->numberBetween(1,2),
            'type'=>fake()->numberBetween(1,3),
            'category'=>fake()->numberBetween(1,4),
            'subcategory' => CatalogSubCategoryItem::inRandomOrder()->first()->id,
            'status' => 1
        ];
    }
}
