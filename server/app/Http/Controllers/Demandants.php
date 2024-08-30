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
            ['organ', 'unit']
        );
    }

    public function selects(Request $request)
    {
        $selections = [
            'units' => Utils::map_select(Data::find(new Unit(), [], ['name'])),
            'sectors' => Utils::map_select(Data::find(new Sector(), [], ['name'])),
            'status' => Demandant::list_status(),
        ];

        if ($request->key == 'filter') {
            $selections['sectors'] = Data::find(new Sector(), ['unit' => $request->search], ['name']);
        }

        return Response()->json($selections, 200);
    }
}
