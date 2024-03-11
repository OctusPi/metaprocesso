<?php

namespace Tests\Unit;

use App\Models\Contract;
use App\Models\Organ;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class ContractModelTest extends TestCase
{
    private Organ $organ;
    private Unit $unit;
    private Supplier $supplier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organ = Organ::factory()->create();
        $this->unit = Unit::factory()->create();
        $this->supplier = Supplier::factory()->create();
    }
    
    public function test_contract_fullfilled(): void
    {
        $contract = (new Contract())->fill([
            'cod' => fake()->text(),
            'category' => fake()->numberBetween(1, 5),
            'obj' => fake()->text(),
            'description' => fake()->text(),
            'organ' => $this->organ->id,
            'unit' => $this->unit->id,
            'date_ini' => fake()->date(),
            'date_fin' => fake()->date(),
            'estimated_value' => fake()->randomFloat(2, 1000, 10000),
            'approved_value' => fake()->randomFloat(2, 1000, 10000),
            'supplier' => $this->supplier->id,
            'additive' => fake()->boolean(),
            'status' => fake()->numberBetween(1, 3)
        ]);

        $this->assertTrue($contract->save());
    }

    public function test_contract_nullables(): void
    {
        $contract = (new Contract())->fill([
            'cod' => fake()->text(),
            'category' => fake()->numberBetween(1, 5),
            'obj' => fake()->text(),
            'organ' => $this->organ->id,
            'unit' => $this->unit->id,
            'date_ini' => fake()->date(),
            'date_fin' => fake()->date(),
            'estimated_value' => fake()->randomFloat(2, 1000, 10000),
            'approved_value' => fake()->randomFloat(2, 1000, 10000),
            'supplier' => $this->supplier->id,
            'additive' => fake()->boolean(),
            'status' => fake()->numberBetween(1, 3)
        ]);

        $this->assertTrue($contract->save());
    }

    public function test_contract_invalid(): void
    {
        $this->expectException(QueryException::class);

        $contract = (new Contract())->fill([
            'cod' => fake()->text(),
            'category' => fake()->numberBetween(1, 5),
            'obj' => fake()->text(),
            'description' => fake()->text(),
            'organ' => $this->organ->id,
            'unit' => $this->unit->id,
            'date_ini' => fake()->date(),
            'date_fin' => fake()->date(),
            'estimated_value' => fake()->randomFloat(2, 1000, 10000),
            'approved_value' => fake()->randomFloat(2, 1000, 10000),
            'supplier' => 0,
            'additive' => fake()->boolean(),
            'status' => fake()->numberBetween(1, 3)
        ]);

        $contract->save();
    }
}
