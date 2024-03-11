<?php

namespace Tests\Unit;

use App\Models\Organ;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class OrganModelTest extends TestCase
{
    public function test_organ_fullfilled(): void
    {
        $organ = (new Organ())->fill([
            'name' => fake()->company(),
            'cnpj' => fake()->numerify('##############'),
            'phone' => fake()->numerify('(##) #########'),
            'email' => fake()->email(),
            'address' => fake()->address(),
            'postalcity' => 1,
            'postalcode' => fake()->postcode(),
            'status' => 1
        ]);

        $this->assertTrue($organ->save());   
    }

    public function test_organ_nullables(): void
    {
        $organ = (new Organ())->fill([
            'name' => fake()->company(),
            'cnpj' => fake()->numerify('##############'),
            'phone' => fake()->numerify('(##) #########'),
            'email' => fake()->email(),
            'address' => fake()->address(),
            'postalcity' => 1,
            'postalcode' => fake()->postcode(),
            'status' => 1
        ]);

        $this->assertTrue($organ->save());
    }

    public function test_organ_invalid(): void
    {
        $this->expectException(QueryException::class);

        $organ = (new Organ())->fill([
            'name' => fake()->company(),
            'cnpj' => fake()->numerify('##############'),
            'phone' => fake()->numerify('(##) #########'),
            'email' => fake()->email(),
            'address' => null,
            'postalcity' => 1,
            'postalcode' => fake()->postcode(),
            'status' => 1
        ]);

        $organ->save();
    }
}
