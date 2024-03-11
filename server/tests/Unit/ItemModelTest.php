<?php

namespace Tests\Unit;

use App\Models\Item;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class ItemModelTest extends TestCase
{
    public function test_item_fullfilled(): void
    {
        $item = (new Item())->fill([
            'cod' => fake()->text(50),
            'name' => fake()->word(),
            'description' => fake()->paragraph(),
            'unit_id' => fake()->word(),
            'volume' => fake()->text(50)
        ]);

        $this->assertTrue($item->save());
    }

    public function test_item_invalid(): void
    {
        $this->expectException(QueryException::class);

        $item = (new Item())->fill([
            'cod' => fake()->text(50),
            'name' => fake()->word(),
            'description' => fake()->text(),
            'unit_id' => fake()->word(),
            'volume' => null
        ]);

        $item->save();
    }
}
