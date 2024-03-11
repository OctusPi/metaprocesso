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
            'organ' => $this->organ->id,
            'unit' => $this->unit->id
        ]);

        $this->assertTrue($dfd->save());
    }

    public function test_dfd_nullables(): void
    {
        $dfd = (new Dfd())->fill([
            'cod' => fake()->text(20),
            'date_ini' => fake()->date(),
            'category' => 1,
            'obj' => fake()->text(),
            'organ' => $this->organ->id,
            'unit' => $this->unit->id
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
            'organ' => 0,
            'unit' => $this->unit->id
        ]);

        $dfd->save();
    }
}
