<?php

namespace Tests\Unit;

use App\Models\Item;
use App\Models\Organ;
use App\Models\Sector;
use App\Models\Stockout;
use App\Models\StockoutItem;
use App\Models\Unit;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class StockoutItemModelTest extends TestCase
{
    private Stockout $stockout;
    private Item $item;

    protected function setUp(): void
    {
        parent::setUp();

        Organ::factory()->create();
        Unit::factory()->create();
        Sector::factory()->create();

        $this->stockout = Stockout::factory()->create();
        $this->item = Item::factory()->create();
    }

    public function test_stock_out_item_fullfilled(): void
    {
        $stockOut = (new StockoutItem())->fill([
            'stockout_id' => $this->stockout->id,
            'item_id' => $this->item->id,
            'quantity' => fake()->numberBetween(1, 100),
        ]);

        $this->assertTrue($stockOut->save());

        $this->assertEquals($stockOut->stockout->id, $this->stockout->id);
        $this->assertEquals($stockOut->item->id, $this->item->id);
    }

    public function test_stock_out_item_invalid(): void
    {
        $this->expectException(QueryException::class);

        $stockOut = (new StockoutItem())->fill([
            'stockout_id' => 0,
            'item_id' => $this->item->id,
            'quantity' => fake()->numberBetween(1, 100),
        ]);

        $stockOut->save();
    }
}
