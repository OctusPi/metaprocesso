<?php

namespace Tests\Unit;

use App\Models\Organ;
use App\Models\Program;
use App\Models\Unit;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class ProgramModelTest extends TestCase
{
    private Organ $organ;
    private Unit $unit;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organ = Organ::factory()->create();
        $this->unit = Unit::factory()->create();
    }

    public function test_program_fullfilled(): void
    {
        $program = (new Program())->fill([
            'name' => fake()->company(),
            'organ_id' => $this->organ->id,
            'unit_id' => $this->unit->id,
            'law' => fake()->text(),
            'description' => fake()->paragraph(),
            'status' => 1
        ]);

        $this->assertTrue($program->save());

        $this->assertEquals($program->organ->id, $this->organ->id);
        $this->assertEquals($program->unit->id, $this->unit->id);
    }

    public function test_program_nullables(): void
    {
        $program = (new Program())->fill([
            'name' => fake()->company(),
            'organ_id' => $this->organ->id,
            'unit_id' => $this->unit->id,
            'status' => 1
        ]);

        $this->assertTrue($program->save());
    }

    public function test_program_invalid(): void
    {
        $this->expectException(QueryException::class);

        $program = (new Program())->fill([
            'name' => fake()->company(),
            'organ_id' => 0,
            'unit_id' => $this->unit->id,
            'law' => fake()->text(255),
            'description' => fake()->paragraph(),
            'status' => 1
        ]);

        $program->save();
    }
}
