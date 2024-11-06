<?php

namespace Database\Factories;


use App\Models\CatalogItem;
use App\Models\Organ;
use App\Models\Catalog;
use App\Models\CatalogSubCategoryItem;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CatalogItem>
 */
class CatalogItemFactory extends Factory
{
    protected $model = CatalogItem::class;

    public function definition(): array
    {
        $faker = Faker::create('pt_BR');

        return [
            'status' => CatalogItem::S_ACTIVE,
            'origin' => $faker->randomElement(CatalogItem::list_origins())['id'],
            'type' => $faker->randomElement(CatalogItem::list_types())['id'],
            'category' => $faker->randomElement(CatalogItem::list_categories())['id'],
            'subcategory_id' => CatalogSubCategoryItem::latest()->first()->id,
            'organ_id' => Organ::latest()->first()->id,
            'catalog_id' => Catalog::latest()->first()->id,
            'description' => $faker->text(200),
            'code' => $faker->text(12),
            'name' => $faker->company(),
            'und' => $faker->word(),
            'volume' => $faker->word(),
        ];
    }
}
