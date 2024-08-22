<?php

namespace App\Http\Controllers;

use App\Http\Middlewares\Data;
use App\Models\Sector;
use App\Models\Unit;
use App\Models\User;
use App\Utils\Utils;
use Illuminate\Http\Request;

class Sectors extends Controller
{
    public function __construct()
    {
        parent::__construct(Sector::class, User::MOD_SECTORS['module']);
    }

    public function list(Request $request)
    {
        return $this->base_list(
            $request,
            ['unit', 'name', 'description'],
            ['name'],
            ['organ', 'unit'],
            organ: true
        );
    }

    public function selects(Request $request)
    {
        $units = Utils::map_select(Data::find(Unit::class, [[
            'column' => 'organ',
            'operator' => '=',
            'mode' => 'AND',
            'value' => $request->header('X-Custom-Header-Organ'),
        ]], ['name']));

        return Response()->json([
            'units' => $units,
        ], 200);
    }
}
