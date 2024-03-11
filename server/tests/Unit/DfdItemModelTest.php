<?php

namespace Tests\Unit;

use App\Models\Dfd;
use App\Models\DfdItem;
use App\Models\Dotation;
use App\Models\Item;
use App\Models\Organ;
use App\Models\Program;
use App\Models\Unit;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class DfdItemModelTest extends TestCase
{
    private Dfd $dfd;
    private Item $item;
    private Program $program;
    private Dotation $dotation;

    protected function setUp(): void
    {
        parent::setUp();

        Organ::factory()->create();
        Unit::factory()->create();

        $this->dfd = Dfd::factory()->create();
        $this->item = Item::factory()->create();
        $this->program = Program::factory()->create();
        $this->dotation = Dotation::factory()->create();
    }

    public function test_dfd_item_fullfilled(): void
    {
        $dfdItem = (new DfdItem())->fill([
            'dfd' => $this->dfd->id, // Assuming dfd, item, program, and dotation IDs exist in the database
            'item' => $this->item->id,
            'quantity' => fake()->numberBetween(1, 100),
            'program' => $this->program->id,
            'dotation' => $this->dotation->id
        ]);

        $this->assertTrue($dfdItem->save());
    }

    public function test_dfd_item_invalid(): void
    {
        $this->expectException(QueryException::class);

        $dfdItem = (new DfdItem())->fill([
            'dfd' => $this->dfd->id,
            'item' => $this->item->id,
            'quantity' => 0,
            'program' => $this->program->id,
            'dotation' => $this->dotation->id
        ]);

        $dfdItem->save();
    }
}
