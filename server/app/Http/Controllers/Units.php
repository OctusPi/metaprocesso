<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;

class Units extends Controller
{
    public function __construct()
    {
        parent::__construct(Unit::class, User::MOD_UNITS['module']);
    }

    public function list(Request $request)
    {
        return $this->base_list(
            $request,
            ['name', 'cnpj', 'address'],
            ['name'],
            ['organ'],
            organ: true
        );
    }

    public function selects(Request $request)
    {
        return Response()->json([
            'status' => Unit::list_status()
        ], 200);
    }
}
