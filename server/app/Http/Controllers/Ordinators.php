<?php

namespace App\Http\Controllers;

use App\Http\Middlewares\Data;
use App\Models\Ordinator;
use App\Models\Unit;
use App\Models\User;
use App\Utils\Utils;
use Illuminate\Http\Request;

class Ordinators extends Controller
{
    public function __construct()
    {
        parent::__construct(Ordinator::class, User::MOD_ORDINATORS['module']);
    }

    public function list(Request $request)
    {
        return $this->base_list(
            $request,
            ['unit', 'name', 'status'],
            ['name'],
            ['organ', 'unit'],
            organ: true
        );
    }

    public function selects(Request $request)
    {
        $units = Utils::map_select(Data::find(Unit::class, [
            [
                'column' => 'organ',
                'operator' => '=',
                'mode' => 'AND',
                'value' => $request->header('X-Custom-Header-Organ'),
            ]
        ], ['name']));

        return Response()->json([
            'units' => $units,
            'status' => Ordinator::list_status(),
        ], 200);
    }
}
