<?php

namespace Database\Factories;


use App\Models\CatalogSubCategoryItem;
use App\Models\Organ;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CatalogSubCategoryItem>
 */
class CatalogSubCategoryItemFactory extends Factory
{
    protected $model = CatalogSubCategoryItem::class;

    public function definition(): array
    {
        $faker = Faker::create('pt_BR');

        return [
            'organ_id' => Organ::latest()->first()->id,
            'name' => $faker->name(),
        ];
    }
}
