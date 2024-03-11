<?php

namespace Tests\Unit;

use App\Models\Contract;
use App\Models\Organ;
use App\Models\PurchaseOrder;
use App\Models\StockEntry;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class StockEntryModelTest extends TestCase
{
    private PurchaseOrder $purchaseOrder;
    private Contract $contract;

    protected function setUp(): void
    {
        parent::setUp();

        Organ::factory()->create();
        Unit::factory()->create();
        Supplier::factory()->create();

        $this->contract = Contract::factory()->create();
        $this->purchaseOrder = PurchaseOrder::factory()->create();
    }

    public function test_stock_entry_fullfilled(): void
    {
        $stockEntry = (new StockEntry())->fill([
            'date_ini' => fake()->date(),
            'invoice' => fake()->text(50),
            'danfe' => fake()->text(),
            'purchaseorder' => $this->purchaseOrder->id,
            'contract' => $this->contract->id,
            'quantity' => fake()->numberBetween(1, 100),
            'current_value' => fake()->randomFloat(2, 10, 1000)
        ]);

        $this->assertTrue($stockEntry->save());
    }

    public function test_stock_entry_invalid(): void
    {
        $this->expectException(QueryException::class);

        $stockEntry = (new StockEntry())->fill([
            'date_ini' => fake()->date(),
            'invoice' => fake()->text(50),
            'danfe' => fake()->text(),
            'purchaseorder' => $this->purchaseOrder->id,
            'contract' => $this->contract->id,
            'quantity' => 0,
            'current_value' => fake()->randomFloat(2, 10, 1000)
        ]);

        $stockEntry->save();
    }
}
