<?php

namespace Database\Factories;


use App\Models\CatalogItem;
use App\Models\Dfd;
use App\Models\Dotation;
use App\Models\Program;
use App\Models\Unit;
use App\Models\User;
use App\Models\Organ;
use App\Models\Comission;
use App\Models\Demandant;
use App\Models\Ordinator;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DfdItem>
 */
class DfdItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'dfd' => Dfd::inRandomOrder()->first()->id,
            'item' => CatalogItem::inRandomOrder()->first()->id,
            'quantity' => fake()->randomNumber(5),
            'program' => Program::inRandomOrder()->first()->id,
            'dotation' => Dotation::inRandomOrder()->first()->id
        ];
    }
}
