<?php

namespace Database\Factories;

use App\Models\Organ;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sector>
 */
class SectorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'organ_id' => Organ::latest()->first()->id,
            'unit_id' =>  Unit::latest()->first()->id,
            'name' => fake()->company()
        ];
    }
}
