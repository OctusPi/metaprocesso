<?php

namespace App\Http\Controllers;

use App\Http\Middlewares\Data;
use App\Models\Demandant;
use App\Models\Sector;
use App\Models\Unit;
use App\Models\User;
use App\Utils\Utils;
use Illuminate\Http\Request;

class Demandants extends Controller
{
    public function __construct()
    {
        parent::__construct(Demandant::class, User::MOD_DEMANDANTS['module']);
    }

    public function list(Request $request)
    {
        return $this->base_list(
            $request,
            ['unit', 'sector', 'name'],
            ['name'],
            ['organ', 'unit'],
            organ: true
        );
    }

    public function selects(Request $request)
    {
        $sectors = $request->key == 'unit' ? Utils::map_select(Data::find(Sector::class, [
            [
                'column' => 'unit',
                'operator' => '=',
                'mode' => 'AND',
                'value' => $request->search,
            ]
        ], ['name'])) : Utils::map_select(Data::find(Sector::class, order: ['name']));

        return Response()->json([
            'sectors' => $sectors,
            'units' => Utils::map_select(Data::find(Unit::class, order: ['name'])),
            'status' => Demandant::list_status(),
        ], 200);
    }
}
