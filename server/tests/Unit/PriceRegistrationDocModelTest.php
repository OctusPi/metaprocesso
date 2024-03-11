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
            'organ' => $this->organ->id,
            'unit' => $this->unit->id,
            'date_ini' => fake()->date(),
            'date_fin' => fake()->date(),
            'estimated_value' => fake()->randomFloat(2, 1, 10000),
            'approved_value' => fake()->randomFloat(2, 1, 10000),
            'supplier' => $this->supplier->id,
            'additive' => fake()->boolean(),
            'status' => fake()->numberBetween(1, 3)
        ]);

        $this->assertTrue($priceRegistrationDoc->save());
    }

    public function test_price_registration_doc_nullables(): void
    {
        $this->expectException(QueryException::class);

        $priceRegistrationDoc = (new PriceRegistrationDoc())->fill([
            'cod' => fake()->text(35),
            'category' => fake()->numberBetween(1, 5),
            'obj' => fake()->text(),
            'organ' => fake()->numberBetween(1, 10),
            'unit' => fake()->numberBetween(1, 10),
            'date_ini' => fake()->date(),
            'date_fin' => fake()->date(),
            'estimated_value' => fake()->randomFloat(2, 1, 10000),
            'approved_value' => fake()->randomFloat(2, 1, 10000),
            'supplier' => fake()->numberBetween(1, 10),
            'additive' => fake()->boolean(),
            'status' => fake()->numberBetween(1, 3)
        ]);

        $priceRegistrationDoc->save();
    }

    public function test_price_registration_doc_invalid(): void
    {
        $this->expectException(QueryException::class);

        $priceRegistrationDoc = (new PriceRegistrationDoc())->fill([
            'cod' => fake()->text(35),
            'category' => fake()->numberBetween(1, 5),
            'obj' => fake()->text(),
            'description' => fake()->text(),
            'organ' => fake()->numberBetween(1, 10),
            'unit' => fake()->numberBetween(1, 10),
            'date_ini' => fake()->date(),
            'date_fin' => fake()->date(),
            'estimated_value' => fake()->randomFloat(2, 1000, 10000),
            'approved_value' => fake()->randomFloat(2, 1000, 10000),
            'supplier' => fake()->numberBetween(1, 10),
            'additive' => fake()->boolean(),
            'status' => fake()->numberBetween(1, 3)
        ]);

        $priceRegistrationDoc->save();
    }
}
