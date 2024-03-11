<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' => fake()->password(),
            'username' => fake()->userName(),
            'sectors' => ['sector_id'],
            'organs' => ['organ_id'],
            'units' => ['unit_id'],
            'modules' => ['module'],
            'profile' => fake()->numberBetween(0, 3),
            'status' => fake()->numberBetween(0, 3),
        ];
    }
}
