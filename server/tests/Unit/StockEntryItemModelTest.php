<?php

namespace Tests\Unit;

use App\Models\Contract;
use App\Models\Item;
use App\Models\Organ;
use App\Models\PurchaseOrder;
use App\Models\StockEntry;
use App\Models\StockEntryItem;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class StockEntryItemModelTest extends TestCase
{
    private StockEntry $stockEntry;
    private Item $item;

    protected function setUp(): void
    {
        parent::setUp();

        Organ::factory()->create();
        Unit::factory()->create();
        Supplier::factory()->create();
        Contract::factory()->create();
        PurchaseOrder::factory()->create();

        $this->stockEntry = StockEntry::factory()->create();
        $this->item = Item::factory()->create();
    }

    public function test_stock_entry_item_fullfilled(): void
    {
        $stockEntryItem = (new StockEntryItem())->fill([
            'stockentry' => $this->stockEntry->id,
            'item' => $this->item->id,
            'quantity' => fake()->numberBetween(1, 100),
            'current_value' => fake()->randomFloat(2, 10, 1000)
        ]);

        $this->assertTrue($stockEntryItem->save());
    }

    public function test_stock_entry_item_invalid(): void
    {
        $this->expectException(QueryException::class);

        $stockEntryItem = (new StockEntryItem())->fill([
            'stockentry' => 0,
            'item' => fake()->numberBetween(1, 10),
            'quantity' => fake()->numberBetween(1, 100),
            'current_value' => fake()->randomFloat(2, 10, 1000)
        ]);

        $stockEntryItem->save();
    }
}
