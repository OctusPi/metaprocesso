<?php

namespace App\Http\Controllers;

use App\Models\Organ;
use App\Models\Common;
use Illuminate\Http\Request;

class Organs extends Controller
{
    public function __construct()
    {
        parent::__construct(Organ::class, true, Common::MOD_ORGANS['module']);
    }

    public function list(Request $request)
    {
        return $this->baseList(['name', 'cnpj', 'postalcity'], ['name']);
    }

    public function selects(Request $request)
    {
        return Response()->json([
            'status' => Organ::list_status()
        ], 200);
    }
}
