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
            'sectors' => ['sector'],
            'organs' => ['organ'],
            'units' => ['unit'],
            'modules' => ['module'],
            'profile' => 1,
            'status' => 1
        ];
    }
}
