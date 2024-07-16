<?php

namespace App\Http\Controllers;

use App\Models\Common;
use App\Models\Supplier;
use Illuminate\Http\Request;

class Suppliers extends Controller
{
    public function __construct()
    {
        parent::__construct(Supplier::class, true, Common::MOD_CATALOGS['module']);
    }

    public function list(Request $request)
    {
        return $this->baseList(['name', 'cnpj', 'address'], ['name']);
    }

    public function selects(Request $request)
    {
        return Response()->json([
            'modalities' => Supplier::list_modalitys(),
            'sizes' => Supplier::list_sizes(),
        ], 200);
    }
}
