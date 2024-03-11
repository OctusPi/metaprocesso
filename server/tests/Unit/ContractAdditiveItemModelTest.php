<?php

namespace Tests\Unit;

use App\Models\Contract;
use App\Models\ContractAdditive;
use App\Models\ContractAdditiveItem;
use App\Models\Item;
use App\Models\Organ;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class ContractAdditiveItemModelTest extends TestCase
{
    private Contract $contract;
    private ContractAdditive $contractAdditive;
    private Item $item;

    protected function setUp(): void
    {
        parent::setUp();

        Organ::factory()->create();
        Unit::factory()->create();
        Supplier::factory()->create();

        $this->contract = Contract::factory()->create();
        $this->contractAdditive = ContractAdditive::factory()->create();
        $this->item = Item::factory()->create();
    }

    public function test_contract_additive_item_fullfilled(): void
    {
        $contractAdditiveItem = (new ContractAdditiveItem())->fill([
            'contract' => $this->contract->id,
            'contractadditive' => $this->contractAdditive->id,
            'item' => $this->item->id,
            'quantity' => fake()->numberBetween(1, 100),
            'unitary_value' => fake()->randomFloat(2, 10, 1000)
        ]);

        $this->assertTrue($contractAdditiveItem->save());
    }

    public function test_contract_additive_item_invalid(): void
    {
        $this->expectException(QueryException::class);

        $contractAdditiveItem = (new ContractAdditiveItem())->fill([
            'contract' => 0,
            'contractadditive' => $this->contractAdditive->id,
            'item' => $this->item->id,
            'quantity' => fake()->numberBetween(1, 100),
            'unitary_value' => fake()->randomFloat(2, 10, 1000)
        ]);

        $contractAdditiveItem->save();
    }
}
