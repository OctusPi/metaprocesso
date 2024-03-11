<?php

namespace Tests\Unit;

use App\Models\Contract;
use App\Models\Organ;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class PurchaseOrderModelTest extends TestCase
{
    private Organ $organ;
    private Unit $unit;
    private Contract $contract;

    protected function setUp(): void
    {
        parent::setUp();

        Supplier::factory()->create();

        $this->organ = Organ::factory()->create();
        $this->unit = Unit::factory()->create();
        $this->contract = Contract::factory()->create();
    }

    public function test_purchase_order_fullfilled(): void
    {
        $purchaseOrder = (new PurchaseOrder())->fill([
            'cod' => fake()->text(20),
            'date_ini' => fake()->date(),
            'organ' => $this->organ->id,
            'unit' => $this->unit->id,
            'contract' => $this->contract->id,
            'status' => fake()->numberBetween(1, 3)
        ]);

        $this->assertTrue($purchaseOrder->save());
    }

    public function test_purchase_order_invalid(): void
    {
        $this->expectException(QueryException::class);

        $purchaseOrder = (new PurchaseOrder())->fill([
            'cod' => fake()->text(20),
            'date_ini' => fake()->date(),
            'organ' => $this->organ->id,
            'unit' => 0,
            'contract' => $this->contract->id,
            'status' => fake()->numberBetween(1, 3)
        ]);

        $purchaseOrder->save();
    }
}
