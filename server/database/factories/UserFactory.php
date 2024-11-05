<?php

namespace Database\Factories;

use App\Models\Organ;
use App\Models\Sector;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        $faker = Faker::create('pt_BR');
        $email = $faker->email();

        return [
            'email' => $email,
            'username' => $email,
            'name' => $faker->name(),
            'password' => 'senha123',
            'sectors' => Sector::all()->pluck('id'),
            'organs' => Organ::all()->toArray(),
            'units' => Unit::all()->toArray(),
            'modules' => User::list_modules(),
            'profile' => $faker->randomElement(array_keys(User::list_profiles())),
            'status' => $faker->numberBetween(0, 2),
        ];
    }
}
