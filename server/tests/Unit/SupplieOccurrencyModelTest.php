<?php

namespace Tests\Unit;

use App\Models\Contract;
use App\Models\Organ;
use App\Models\PurchaseOrder;
use App\Models\Sector;
use App\Models\SupplieOcurrency;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class SupplieOccurrencyModelTest extends TestCase
{
    private PurchaseOrder $purchaseOrder;
    private Contract $contract;
    private Supplier $supplier;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Organ::factory()->create();
        Unit::factory()->create();
        Sector::factory()->create();

        $this->user = User::factory()->create();
        $this->supplier = Supplier::factory()->create();
        $this->contract = Contract::factory()->create();
        $this->purchaseOrder = PurchaseOrder::factory()->create();
    }

    public function test_stock_out_item_fullfilled(): void
    {
        $stockOut = (new SupplieOcurrency())->fill([
            'purchaseorder_id' => $this->purchaseOrder->id,
            'contract_id' => $this->contract->id,
            'supplier_id' => $this->supplier->id,
            'status' => fake()->numberBetween(1, 3),
            'description' => fake()->text(),
            'user_id' => $this->user->id,
        ]);

        $this->assertTrue($stockOut->save());

        $this->assertEquals($stockOut->contract->id, $this->contract->id);
        $this->assertEquals($stockOut->purchaseOrder->id, $this->purchaseOrder->id);
        $this->assertEquals($stockOut->supplier->id, $this->supplier->id);
        $this->assertEquals($stockOut->user->id, $this->user->id);
    }

    public function test_stock_out_item_invalid(): void
    {
        $this->expectException(QueryException::class);

        $stockOut = (new SupplieOcurrency())->fill([
            'purchaseorder_id' => $this->purchaseOrder->id,
            'contract_id' => $this->contract->id,
            'supplier_id' => $this->supplier->id,
            'status' => fake()->numberBetween(1, 3),
            'description' => fake()->text(),
            'user_id' => 0,
        ]);

        $stockOut->save();
    }
}
