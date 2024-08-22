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
        $units = Utils::map_select(Data::find(Unit::class, [
            [
                'column' => 'organ',
                'operator' => '=',
                'mode' => 'AND',
                'value' => $request->header('X-Custom-Header-Organ'),
            ]
        ], ['name']));

        $sectors = $request->key == 'unit' ? Utils::map_select(Data::find(Sector::class, [
            [
                'column' => 'unit',
                'operator' => '=',
                'mode' => 'AND',
                'value' => $request->search,
            ]
        ], ['name'])) : Utils::map_select(Data::find(Sector::class, order: ['name']));

        return Response()->json([
            'units' => $units,
            'sectors' => $sectors,
            'status' => Demandant::list_status(),
        ], 200);
    }
}
