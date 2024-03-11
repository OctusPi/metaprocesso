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
        $this->unit = Unit::factory()->create();
    }

    public function test_sector_fullfilled(): void
    {
        $sector = (new Sector())->fill([
            'organ_id' => $this->organ->id,
            'unit_id' => $this->unit->id,
            'name' => fake()->company(),
            'cnpj' => fake()->numerify('##############'),
            'phone' => fake()->numerify('(##) #########'),
            'email' => fake()->email(),
            'address' => fake()->address()
        ]);

        $this->assertTrue($sector->save());

        $this->assertEquals($sector->organ->id, $this->organ->id);
        $this->assertEquals($sector->unit->id, $this->unit->id);
    }

    public function test_sector_nullables(): void
    {
        $sector = (new Sector())->fill([
            'organ_id' => $this->organ->id,
            'unit_id' => $this->unit->id,
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
            'organ_id' => 0,
            'unit_id' => $this->unit->id,
            'name' => fake()->company(),
            'cnpj' => fake()->numerify('##############'),
            'phone' => fake()->numerify('(##) #########'),
            'email' => fake()->email(),
            'address' => fake()->address()
        ]);

        $sector->save();
    }
}
