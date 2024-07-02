<?php

namespace App\Http\Controllers;

use App\Middleware\Data;
use App\Models\Comission;
use App\Models\ComissionMember;
use App\Models\Organ;
use App\Models\Unit;
use App\Utils\Utils;
use Illuminate\Http\Request;

class PriceRecords extends Controller
{
    

    public function selects(Request $request)
    {
        //feed selects form
        if ($request->key != 'comission') {
            $units = [];
            $comissions = [];


            if ($request->key) {
                $units = $request->key == 'organ' ? Utils::map_select(Data::list(Unit::class, [
                    [
                        'column' => $request->key,
                        'operator' => '=',
                        'value' => $request->search,
                        'mode' => 'AND'
                    ]
                ], ['name'])) : Utils::map_select(Data::list(Unit::class));

                $comissions = Utils::map_select(Data::list(Comission::class, [
                    [
                        'column' => $request->key,
                        'operator' => '=',
                        'value' => $request->search,
                        'mode' => 'AND'
                    ]
                ], ['name']));

            }

            return Response()->json([
                'organs' => Utils::map_select(Data::list(Organ::class, order: ['name'])),
                'units' => $units,
                'comissions' => $comissions,
            ], 200);
        }

        //rescue comission_members
        $comissions_members = Data::list(ComissionMember::class, ['comission' => $request->search], ['responsibility']);
        return Response()->json($comissions_members);

    }
}
