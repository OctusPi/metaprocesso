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
            'organ' => $this->organ->id,
            'unit' => $this->unit->id,
            'sector' => $this->sector->id,
            'cod' => fake()->text(20),
            'date_ini' => fake()->date(),
            'description' => fake()->text(),
            'issuer' => fake()->name(),
            'requester' => fake()->name()
        ]);

        $this->assertTrue($stockOut->save());
    }

    public function test_stock_out_item_nullables(): void
    {
        $stockOut = (new Stockout())->fill([
            'organ' => $this->organ->id,
            'unit' => $this->unit->id,
            'sector' => $this->sector->id,
            'cod' => fake()->text(20),
            'date_ini' => fake()->date(),
        ]);

        $this->assertTrue($stockOut->save());
    }

    public function test_stock_out_invalid(): void
    {
        $this->expectException(QueryException::class);

        $stockOut = (new Stockout())->fill([
            'organ' => $this->organ->id,
            'unit' => $this->unit->id,
            'sector' => 0,
            'cod' => fake()->text(20),
            'date_ini' => fake()->date(),
            'description' => fake()->text(),
            'issuer' => fake()->name(),
            'requester' => fake()->name()
        ]);

        $stockOut->save();
    }
}
