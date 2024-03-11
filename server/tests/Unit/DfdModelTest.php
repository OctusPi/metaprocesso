<?php

namespace Tests\Unit;

use App\Models\Dfd;
use App\Models\Organ;
use App\Models\Unit;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class DfdModelTest extends TestCase
{
    private Organ $organ;
    private Unit $unit;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organ = Organ::factory()->create();
        $this->unit = Unit::factory()->create();
    }

    public function test_dfd_fullfilled(): void
    {
        $dfd = (new Dfd())->fill([
            'cod' => fake()->text(20),
            'date_ini' => fake()->date(),
            'category' => 1,
            'obj' => fake()->text(),
            'description' => fake()->text(),
            'organ_id' => $this->organ->id,
            'unit_id' => $this->unit->id
        ]);

        $this->assertTrue($dfd->save());

        $this->assertEquals($dfd->organ->id, $this->organ->id);
        $this->assertEquals($dfd->unit->id, $this->unit->id);
    }

    public function test_dfd_nullables(): void
    {
        $dfd = (new Dfd())->fill([
            'cod' => fake()->text(20),
            'date_ini' => fake()->date(),
            'category' => 1,
            'obj' => fake()->text(),
            'organ_id' => $this->organ->id,
            'unit_id' => $this->unit->id
        ]);

        $this->assertTrue($dfd->save());
    }

    public function test_dfd_invalid(): void
    {
        $this->expectException(QueryException::class);

        $dfd = (new Dfd())->fill([
            'cod' => fake()->text(20),
            'date_ini' => fake()->date(),
            'category' => 1,
            'obj' => fake()->text(),
            'description' => fake()->text(),
            'organ_id' => 0,
            'unit_id' => $this->unit->id
        ]);

        $dfd->save();
    }
}
