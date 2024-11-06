<?php

namespace Database\Factories;


use App\Models\CatalogItem;
use App\Models\Dfd;
use App\Models\DfdItem;
use App\Models\Dotation;
use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DfdItem>
 */
class DfdItemFactory extends Factory
{
    protected $model = DfdItem::class;

    public function definition(): array
    {
        $faker = Faker::create('pt_BR');

        return [
            'dfd_id' => Dfd::latest()->first()->id,
            'item_id' => CatalogItem::latest()->first()->id,
            'quantity' => $faker->randomNumber(5),
            'program_id' => Program::latest()->first()->id,
            'dotation_id' => Dotation::latest()->first()->id
        ];
    }
}
