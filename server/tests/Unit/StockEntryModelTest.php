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
            'date_ini' => "12/12/2023",
            'invoice' => fake()->text(50),
            'danfe' => fake()->text(),
            'purchaseorder_id' => $this->purchaseOrder->id,
            'contract_id' => $this->contract->id,
            'quantity' => fake()->numberBetween(1, 100),
            'current_value' => fake()->randomFloat(2, 10, 1000)
        ]);

        $this->assertTrue($stockEntry->save());

        $this->assertEquals($stockEntry->purchaseOrder->id, $this->purchaseOrder->id);
        $this->assertEquals($stockEntry->contract->id, $this->contract->id);
        $this->assertEquals($stockEntry->date_ini, "12/12/2023");
    }

    public function test_stock_entry_invalid(): void
    {
        $this->expectException(QueryException::class);

        $stockEntry = (new StockEntry())->fill([
            'date_ini' => "12/12/2023",
            'invoice' => fake()->text(50),
            'danfe' => fake()->text(),
            'purchaseorder_id' => $this->purchaseOrder->id,
            'contract_id' => 0,
            'quantity' => fake()->numberBetween(1, 100),
            'current_value' => fake()->randomFloat(2, 10, 1000)
        ]);

        $stockEntry->save();
    }
}
