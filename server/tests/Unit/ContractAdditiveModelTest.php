<?php

namespace Tests\Unit;

use App\Models\Contract;
use App\Models\ContractAdditive;
use App\Models\Organ;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class ContractAdditiveModelTest extends TestCase
{
    private Contract $contract;

    protected function setUp(): void
    {
        parent::setUp();

        Organ::factory()->create();
        Unit::factory()->create();
        Supplier::factory()->create();

        $this->contract = Contract::factory()->create();
    }

    public function test_contract_additive_fullfilled(): void
    {
        $contractAdditive = (new ContractAdditive())->fill([
            'contract_id' => $this->contract->id,
            'date_ini' => fake()->date(),
            'obj' => fake()->text(),
            'description' => fake()->text()
        ]);

        $this->assertTrue($contractAdditive->save());

        $this->assertEquals($contractAdditive->contract->id, $this->contract->id);
    }

    public function test_contract_additive_nullables(): void
    {
        $contractAdditive = (new ContractAdditive())->fill([
            'contract_id' => $this->contract->id,
            'date_ini' => fake()->date(),
            'obj' => fake()->text(),
        ]);

        $this->assertTrue($contractAdditive->save());
    }

    public function test_contract_additive_invalid(): void
    {
        $this->expectException(QueryException::class);

        $contractAdditive = (new ContractAdditive())->fill([
            'contract_id' => 0,
            'date_ini' => fake()->date(),
            'obj' => fake()->text(),
            'description' => fake()->text()
        ]);

        $contractAdditive->save();
    }
}
