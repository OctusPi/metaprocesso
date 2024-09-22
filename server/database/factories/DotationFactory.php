<?php

namespace Database\Factories;

use App\Models\Dotation;
use App\Models\Organ;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dotation>
 */
class DotationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'status' => fake()->randomElement(Dotation::list_status())['id'],
            'organ' => Organ::latest()->first()->id,
            'unit' => Unit::latest()->first()->id,
            'description' => fake()->text(200),
            'name' => fake()->company(),
            'law' => fake()->text(200),
        ];
    }
}
