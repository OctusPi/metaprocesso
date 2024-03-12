<?php

namespace Tests\Unit;

use App\Models\Organ;
use App\Models\Sector;
use App\Models\Stockout;
use App\Models\Unit;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class StockoutModelTest extends TestCase
{
    private Organ $organ;
    private Unit $unit;
    private Sector $sector;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organ = Organ::factory()->create();
        $this->unit = Unit::factory()->create();
        $this->sector = Sector::factory()->create();
    }

    public function test_stock_out_fullfilled(): void
    {
        $stockOut = (new Stockout())->fill([
            'organ_id' => $this->organ->id,
            'unit_id' => $this->unit->id,
            'sector_id' => $this->sector->id,
            'cod' => fake()->text(20),
            'date_ini' => "12/12/2023",
            'description' => fake()->text(),
            'issuer' => fake()->name(),
            'requester' => fake()->name()
        ]);

        $this->assertTrue($stockOut->save());

        $this->assertEquals($stockOut->organ->id, $this->organ->id);
        $this->assertEquals($stockOut->unit->id, $this->unit->id);
        $this->assertEquals($stockOut->sector->id, $this->sector->id);
        $this->assertEquals($stockOut->date_ini, "12/12/2023");
    }

    public function test_stock_out_item_nullables(): void
    {
        $stockOut = (new Stockout())->fill([
            'organ_id' => $this->organ->id,
            'unit_id' => $this->unit->id,
            'sector_id' => $this->sector->id,
            'cod' => fake()->text(20),
            'date_ini' => "12/12/2023",
        ]);

        $this->assertTrue($stockOut->save());
    }

    public function test_stock_out_invalid(): void
    {
        $this->expectException(QueryException::class);

        $stockOut = (new Stockout())->fill([
            'organ_id' => $this->organ->id,
            'unit_id' => $this->unit->id,
            'sector_id' => 0,
            'cod' => fake()->text(20),
            'date_ini' => "12/12/2023",
            'description' => fake()->text(),
            'issuer' => fake()->name(),
            'requester' => fake()->name()
        ]);

        $stockOut->save();
    }
}
