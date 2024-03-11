<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    public function test_user_fullfilled(): void
    {
        $user = (new User())->fill([
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
        ]);

        $this->assertTrue($user->save());   
    }

    public function test_user_nullables(): void
    {
        $user = (new User())->fill([
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' => fake()->password(),
            'username' => fake()->userName(),
            'sectors' => ['sector'],
            'profile' => 1,
            'status' => 1
        ]);

        $this->assertTrue($user->save());  
    }

    public function test_user_invalid(): void
    {
        $this->expectException(QueryException::class);

        $user = (new User())->fill([
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' => fake()->password(),
            'username' => fake()->userName(),
            'sectors' => null,
            'profile' => 1,
            'status' => 1
        ]);

        $user->save();
    }
}
