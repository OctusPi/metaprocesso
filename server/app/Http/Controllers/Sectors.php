<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\User;
use App\Utils\Utils;
use App\Models\Organ;
use App\Models\Sector;
use App\Middleware\Data;
use App\Security\Guardian;
use Illuminate\Http\Request;

class Sectors extends Controller
{
    public function __construct()
    {
        parent::__construct(Sector::class, User::MOD_SECTORS);
        Guardian::validateAccess($this->module_id);
    }

    public function list(Request $request)
    {
        return $this->baseList(['organ', 'unit', 'name'], ['name'], ['organ', 'unit']);
    }

    public function selects(Request $request)
    {
        $units = $request->key ? Utils::map_select(Data::list(Unit::class, [
            [
                'column'   => $request->key,
                'operator' => '=',
                'value'    => $request->search,
                'mode'     => 'AND'
            ]
            ], ['name'])) : Utils::map_select(Data::list(Unit::class));

        return Response()->json([
            'organs' => Utils::map_select(Data::list(Organ::class, order:['name'])),
            'units'  => $units,
        ], 200);
    }
}
