<?php

namespace Tests\Unit;

use App\Models\Organ;
use App\Models\Unit;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class UnitModelTest extends TestCase
{
    private Organ $organ;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->organ = Organ::factory()->create();
    }

    public function test_unit_fullfilled(): void
    {
        $unit = (new Unit())->fill([
            'organ_id' => $this->organ->id,
            'name' => fake()->company(),
            'cnpj' => fake()->numerify('##############'),
            'phone' => fake()->numerify('(##) #########'),
            'email' => fake()->email(),
            'address' => fake()->address()
        ]);

        $this->assertTrue($unit->save());

        $this->assertEquals($unit->organ->toArray(), $this->organ->toArray());
    }

    public function test_unit_nullables(): void
    {
        $unit = (new Unit())->fill([
            'organ_id' => $this->organ->id,
            'name' => fake()->company(),
            'cnpj' => fake()->numerify('##############'),
            'phone' => fake()->numerify('(##) #########'),
            'email' => fake()->email(),
            'address' => fake()->address()
        ]);

        $this->assertTrue($unit->save());

        $this->assertEquals($unit->organ->toArray(), $this->organ->toArray());
    }

    public function test_unit_invalid(): void
    {
        $this->expectException(QueryException::class);

        $unit = (new Unit())->fill([
            'organ_id' => 0,
            'name' => fake()->company(),
            'cnpj' => fake()->numerify('##############'),
            'phone' => fake()->numerify('(##) #########'),
            'email' => fake()->email(),
            'address' => fake()->address()
        ]);

        $unit->save();
    }
}
