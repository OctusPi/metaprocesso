<?php

namespace Database\Factories;

use App\Models\Organ;
use App\Models\Sector;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        $email = fake()->email();

        return [
            'email' => $email,
            'username' => $email,
            'name' => fake()->name(),
            'password' => 'senha123',
            'sectors' => Sector::all()->pluck('id'),
            'organs' => Organ::all()->pluck('id'),
            'units' => Unit::all()->pluck('id'),
            'modules' => User::list_modules(),
            'profile' => fake()->randomElement(array_keys(User::list_profiles())),
            'status' => fake()->numberBetween(0, 2),
        ];
    }
}
