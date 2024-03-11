<?php

namespace Tests\Unit;

use App\Models\Item;
use App\Models\Organ;
use App\Models\PriceRegistrationDoc;
use App\Models\PriceRegistrationDocItem;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class PriceRegistrationDocModelItemTest extends TestCase
{
    private PriceRegistrationDoc $priceRegistrationDoc;
    private Item $item;
    private Supplier $supplier;

    protected function setUp(): void
    {
        parent::setUp();

        Organ::factory()->create();
        Unit::factory()->create();
        Supplier::factory()->create();

        $this->priceRegistrationDoc = PriceRegistrationDoc::factory()->create();
        $this->item = Item::factory()->create();
    }

    public function test_price_registration_doc_item_fullfilled(): void
    {
        $priceRegistrationDocItem = (new PriceRegistrationDocItem())->fill([
            'priceregistrationdoc_id' => $this->priceRegistrationDoc->id,
            'item_id' =>  $this->item->id,
            'quantity' => fake()->numberBetween(1, 100),
            'unitary_value' => fake()->randomFloat(2, 1, 100000)
        ]);

        $this->assertTrue($priceRegistrationDocItem->save());

        $this->assertEquals($priceRegistrationDocItem->priceRegistrationDoc->id, $this->priceRegistrationDoc->id);
        $this->assertEquals($priceRegistrationDocItem->item->id, $this->item->id);
    }

    public function test_price_registration_doc_item_invalid(): void
    {
        $this->expectException(QueryException::class);

        $priceRegistrationDocItem = (new PriceRegistrationDocItem())->fill([
            'priceregistrationdoc_id' => null,
            'item_id' => fake()->numberBetween(1, 10),
            'quantity' => fake()->numberBetween(1, 100),
            'unitary_value' => fake()->randomFloat(2, 10, 1000)
        ]);

        $priceRegistrationDocItem->save();
    }
}
