<?php

namespace Tests\Unit;

use App\Models\Dotation;
use App\Models\Organ;
use App\Models\Unit;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class DotationModelTest extends TestCase
{
    private Organ $organ;
    private Unit $unit;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organ = Organ::factory()->create();
        $this->unit = Unit::factory()->create();
    }

    public function test_dotation_fullfilled(): void
    {
        $dotation = (new Dotation())->fill([
            'name' => fake()->word(),
            'organ_id' => $this->organ->id,
            'unit_id' => $this->unit->id,
            'law' => fake()->word(),
            'description' => fake()->paragraph(),
            'status' => 1
        ]);

        $this->assertTrue($dotation->save());

        $this->assertEquals($dotation->organ->id, $this->organ->id);
        $this->assertEquals($dotation->unit->id, $this->unit->id);
    }

    public function test_dotation_nullables(): void
    {
        $dotation = (new Dotation())->fill([
            'name' => fake()->word(),
            'organ_id' => $this->organ->id,
            'unit_id' => $this->unit->id,
            'status' => 1
        ]);

        $this->assertTrue($dotation->save());
    }

    public function test_dotation_invalid(): void
    {
        $this->expectException(QueryException::class);

        $dotation = (new Dotation())->fill([
            'name' => fake()->word(),
            'organ_id' => 0,
            'unit_id' => $this->unit->id,
            'law' => fake()->word(),
            'description' => fake()->paragraph(),
            'status' => 1
        ]);

        $dotation->save();
    }
}
