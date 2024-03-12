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
            'organ_id' => $this->organ->id,
            'unit_id' => $this->unit->id,
            'date_ini' => "12/12/2023",
            'date_fin' => "12/12/2024",
            'estimated_value' => fake()->randomFloat(2, 1000, 10000),
            'approved_value' => fake()->randomFloat(2, 1000, 10000),
            'supplier_id' => $this->supplier->id,
            'additive' => fake()->boolean(),
            'status' => fake()->numberBetween(1, 3)
        ]);

        $this->assertTrue($contract->save());

        $this->assertEquals($contract->organ->id, $this->organ->id);
        $this->assertEquals($contract->unit->id, $this->unit->id);
        $this->assertEquals($contract->supplier->id, $this->supplier->id);
        $this->assertEquals($contract->date_ini, "12/12/2023");
        $this->assertEquals($contract->date_fin, "12/12/2024");
    }

    public function test_contract_nullables(): void
    {
        $contract = (new Contract())->fill([
            'cod' => fake()->text(),
            'category' => fake()->numberBetween(1, 5),
            'obj' => fake()->text(),
            'organ_id' => $this->organ->id,
            'unit_id' => $this->unit->id,
            'date_ini' => "12/12/2023",
            'date_fin' => "12/12/2024",
            'estimated_value' => fake()->randomFloat(2, 1000, 10000),
            'approved_value' => fake()->randomFloat(2, 1000, 10000),
            'supplier_id' => $this->supplier->id,
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
            'organ_id' => $this->organ->id,
            'unit_id' => $this->unit->id,
            'date_ini' => "12/12/2023",
            'date_fin' => "12/12/2024",
            'estimated_value' => fake()->randomFloat(2, 1000, 10000),
            'approved_value' => fake()->randomFloat(2, 1000, 10000),
            'supplier_id' => 0,
            'additive' => fake()->boolean(),
            'status' => fake()->numberBetween(1, 3)
        ]);

        $contract->save();
    }
}
