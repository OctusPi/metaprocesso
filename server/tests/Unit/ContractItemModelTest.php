<?php

namespace Tests\Unit;

use App\Models\Contract;
use App\Models\ContractItem;
use App\Models\Item;
use App\Models\Organ;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class ContractItemModelTest extends TestCase
{
    private Contract $contract;
    private Item $item;

    protected function setUp(): void
    {
        parent::setUp();

        Organ::factory()->create();
        Unit::factory()->create();
        Supplier::factory()->create();

        $this->contract = Contract::factory()->create();
        $this->item = Item::factory()->create();
    }

    public function test_contract_item_fullfilled(): void
    {
        $contractItem = (new ContractItem())->fill([
            'contract_id' => $this->contract->id,
            'item_id' => $this->item->id,
            'quantity' => fake()->numberBetween(1, 100),
            'unitary_value' => fake()->randomFloat(2, 1, 100000)
        ]);

        $this->assertTrue($contractItem->save());

        $this->assertEquals($contractItem->contract->id, $this->contract->id);
        $this->assertEquals($contractItem->item->id, $this->item->id);
    }

    public function test_contract_item_invalid(): void
    {
        $this->expectException(QueryException::class);

        $contractItem = (new ContractItem())->fill([
            'contract_id' => $this->contract->id,
            'item_id' => 0,
            'quantity' => fake()->numberBetween(1, 100),
            'unitary_value' => fake()->randomFloat(2, 1, 100000)
        ]);

        $contractItem->save();
    }
}
