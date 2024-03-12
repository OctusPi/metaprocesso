<?php

namespace Tests\Unit;

use App\Models\Organ;
use App\Models\PriceRegistrationDoc;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class PriceRegistrationDocModelTest extends TestCase
{
    private Organ $organ;
    private Unit $unit;
    private Supplier $supplier;

    protected function setUp(): void
    {
        parent::setUp();

        Organ::factory()->create();
        Unit::factory()->create();
        Supplier::factory()->create();

        $this->organ = Organ::factory()->create();
        $this->unit = Unit::factory()->create();
        $this->supplier = Supplier::factory()->create();
    }

    public function test_price_registration_doc_fullfilled(): void
    {
        $priceRegistrationDoc = (new PriceRegistrationDoc())->fill([
            'cod' => fake()->text(20),
            'category' => fake()->numberBetween(1, 3),
            'obj' => fake()->text(),
            'description' => fake()->text(),
            'organ_id' => $this->organ->id,
            'unit_id' => $this->unit->id,
            'date_ini' => "12/12/2023",
            'date_fin' => "12/12/2024",
            'estimated_value' => fake()->randomFloat(2, 1, 10000),
            'approved_value' => fake()->randomFloat(2, 1, 10000),
            'supplier_id' => $this->supplier->id,
            'additive' => fake()->boolean(),
            'status' => fake()->numberBetween(1, 3)
        ]);

        $this->assertTrue($priceRegistrationDoc->save());

        $this->assertEquals($priceRegistrationDoc->organ->id, $this->organ->id);
        $this->assertEquals($priceRegistrationDoc->unit->id, $this->unit->id);
        $this->assertEquals($priceRegistrationDoc->supplier->id, $this->supplier->id);
        $this->assertEquals($priceRegistrationDoc->date_ini, "12/12/2023");
        $this->assertEquals($priceRegistrationDoc->date_fin, "12/12/2024");
    }

    public function test_price_registration_doc_nullables(): void
    {
        $priceRegistrationDoc = (new PriceRegistrationDoc())->fill([
            'cod' => fake()->text(35),
            'category' => fake()->numberBetween(1, 5),
            'obj' => fake()->text(),
            'organ_id' => $this->organ->id,
            'unit_id' => $this->unit->id,
            'date_ini' => "12/12/2023",
            'date_fin' => "12/12/2024",
            'estimated_value' => fake()->randomFloat(2, 1, 10000),
            'approved_value' => fake()->randomFloat(2, 1, 10000),
            'supplier_id' => $this->supplier->id,
            'additive' => fake()->boolean(),
            'status' => fake()->numberBetween(1, 3)
        ]);

        $this->assertTrue($priceRegistrationDoc->save());
    }

    public function test_price_registration_doc_invalid(): void
    {
        $this->expectException(QueryException::class);

        $priceRegistrationDoc = (new PriceRegistrationDoc())->fill([
            'cod' => fake()->text(35),
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

        $priceRegistrationDoc->save();
    }
}
