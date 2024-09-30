<?php

namespace Database\Factories;


use App\Models\CatalogItem;
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
            'status' => CatalogItem::S_ACTIVE,
            'origin' => fake()->randomElement(CatalogItem::list_origins())['id'],
            'type' => fake()->randomElement(CatalogItem::list_types())['id'],
            'category' => fake()->randomElement(CatalogItem::list_categories())['id'],
            'subcategory_id' => CatalogSubCategoryItem::latest()->first()->id,
            'organ_id' => Organ::latest()->first()->id,
            'catalog_id' => Catalog::latest()->first()->id,
            'description' => fake()->text(200),
            'code' => fake()->text(12),
            'name' => fake()->company(),
            'und' => fake()->word(),
            'volume' => fake()->word(),
        ];
    }
}
