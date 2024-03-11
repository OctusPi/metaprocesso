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
        $this->unit = Unit::factory()->create([
            'organ' => $this->organ->id
        ]);
    }

    public function test_dotation_fullfilled(): void
    {
        $dotation = (new Dotation())->fill([
            'name' => fake()->word(),
            'organ' => $this->organ->id,
            'unit' => $this->unit->id,
            'law' => fake()->word(),
            'description' => fake()->paragraph(),
            'status' => 1
        ]);

        $this->assertTrue($dotation->save());
    }

    public function test_dotation_nullables(): void
    {
        $dotation = (new Dotation())->fill([
            'name' => fake()->word(),
            'organ' => $this->organ->id,
            'unit' => $this->unit->id,
            'status' => 1
        ]);

        $this->assertTrue($dotation->save());
    }

    public function test_dotation_invalid(): void
    {
        $this->expectException(QueryException::class);

        $dotation = (new Dotation())->fill([
            'name' => fake()->word(),
            'organ' => 0,
            'unit' => $this->unit->id,
            'law' => fake()->word(),
            'description' => fake()->paragraph(),
            'status' => 1
        ]);

        $dotation->save();
    }
}
