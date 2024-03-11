<?php

namespace Tests\Unit;

use App\Models\Supplier;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class SupplierModelTest extends TestCase
{
    public function test_supplier_fullfilled(): void
    {
        $supplier = (new Supplier())->fill([
            'name' => fake()->company(),
            'cnpj' => fake()->numerify('##############'),
            'agent' => fake()->name(),
            'cpf' => fake()->numerify('###########'),
            'phone' => fake()->numerify('(##) #########'),
            'email' => fake()->email(),
            'address' => fake()->address()
        ]);

        $this->assertTrue($supplier->save());
    }

    public function test_supplier_nullables(): void
    {
        $supplier = (new Supplier())->fill([
            'name' => fake()->company(),
            'cnpj' => fake()->numerify('##############'),
            'phone' => fake()->numerify('(##) #########'),
            'email' => fake()->email(),
            'address' => fake()->address()
        ]);

        $this->assertTrue($supplier->save());
    }

    public function test_supplier_invalid(): void
    {
        $this->expectException(QueryException::class);

        $supplier = (new Supplier())->fill([
            'name' => fake()->company(),
            'cnpj' => fake()->numerify('##############'),
            'phone' => fake()->numerify('(##) #########'),
            'email' => fake()->email(),
            'address' => null
        ]);

        $supplier->save();
    }
}
