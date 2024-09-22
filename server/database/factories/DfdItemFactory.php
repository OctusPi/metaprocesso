<?php

namespace Database\Factories;


use App\Models\CatalogItem;
use App\Models\Dfd;
use App\Models\Dotation;
use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DfdItem>
 */
class DfdItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'dfd' => Dfd::latest()->first()->id,
            'item' => CatalogItem::latest()->first()->id,
            'quantity' => fake()->randomNumber(5),
            'program' => Program::latest()->first()->id,
            'dotation' => Dotation::latest()->first()->id
        ];
    }
}
