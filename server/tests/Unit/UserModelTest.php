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
            'sectors' => ['sector_id'],
            'organs' => ['organ_id'],
            'units' => ['unit_id'],
            'modules' => ['module'],
            'profile' => 1,
            'status' => 1
        ]);

        $this->assertTrue($user->save());
    }

    public function test_user_casts(): void
    {
        $user1 = (new User())->fill([
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' => fake()->password(),
            'username' => fake()->userName(),
            'sectors' => json_encode(['val' => 'test']),
            'organs' => json_encode(['val' => 'test']),
            'units' => json_encode(['val' => 'test']),
            'modules' => json_encode(['val' => 'test']),
            'profile' => 1,
            'status' => 1
        ]);

        $user2 = (new User())->fill([
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' => fake()->password(),
            'username' => fake()->userName(),
            'sectors' => ['val' => 'test'],
            'organs' => ['val' => 'test'],
            'units' => ['val' => 'test'],
            'modules' => ['val' => 'test'],
            'profile' => 1,
            'status' => 1,
            'nowlogin' => '12/12/2024 12:12:12',
            'lastlogin' => '12/12/2023 12:12:12'        
        ]);

        $this->assertTrue($user1->save());
        $this->assertTrue($user2->save());
        
        $this->assertEquals($user1->sectors, $user2->sectors);
        $this->assertEquals($user1->organs, $user2->organs);
        $this->assertEquals($user1->units, $user2->units);
        $this->assertEquals($user1->modules, $user2->modules);
    }

    public function test_user_nullables(): void
    {
        $user = (new User())->fill([
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' => fake()->password(),
            'username' => fake()->userName(),
            'sectors' => ['sector_id'],
            'profile' => 1,
            'status' => 1,
            'nowlogin' => null,
            'lastlogin' => null
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
