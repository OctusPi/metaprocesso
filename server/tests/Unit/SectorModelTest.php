<?php

namespace Tests\Unit;

use App\Models\Organ;
use App\Models\Sector;
use App\Models\Unit;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class SectorModelTest extends TestCase
{
    private Organ $organ;
    private Unit $unit;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organ = Organ::factory()->create();
        $this->unit = Unit::factory()->create([
            'organ' => $this->organ->id
        ]);
    }

    public function test_sector_fullfilled(): void
    {
        $sector = (new Sector())->fill([
            'organ' => $this->organ->id,
            'unit' => $this->unit->id,
            'name' => fake()->company(),
            'cnpj' => fake()->numerify('##############'),
            'phone' => fake()->numerify('(##) #########'),
            'email' => fake()->email(),
            'address' => fake()->address()
        ]);

        $this->assertTrue($sector->save());
    }

    public function test_sector_nullables(): void
    {
        $sector = (new Sector())->fill([
            'organ' => $this->organ->id,
            'unit' => $this->unit->id,
            'name' => fake()->company(),
            'cnpj' => fake()->numerify('##############'),
            'phone' => fake()->numerify('(##) #########'),
            'email' => fake()->email(),
            'address' => fake()->address()
        ]);

        $this->assertTrue($sector->save());
    }

    public function test_sector_invalid(): void
    {
        $this->expectException(QueryException::class);

        $sector = (new Sector())->fill([
            'organ' => 0,
            'unit' => $this->unit->id,
            'name' => fake()->company(),
            'cnpj' => fake()->numerify('##############'),
            'phone' => fake()->numerify('(##) #########'),
            'email' => fake()->email(),
            'address' => fake()->address()
        ]);

        $sector->save();
    }
}
