<?php

namespace App\Http\Controllers;

use App\Middleware\Data;
use App\Models\Comission;
use App\Models\ComissionMember;
use App\Models\Organ;
use App\Models\PriceRecord;
use App\Models\Process;
use App\Models\Unit;
use App\Utils\Notify;
use App\Utils\Utils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PriceRecords extends Controller
{
    
    public function list_processes(Request $request){

        if(empty($request->all())){
            return Response()->json(Notify::warning('Informe pelo menos um campo de busca...'), 500);
        }
        
        $search     = Utils::map_search(['protocol', 'organ', 'description'], $request->all());
        $search_obj = Utils::map_search_obj($request->units, 'units', 'id');
        $betw       = $request->date_i && $request->date_f ? ['date_ini' => [$request->date_i, $request->date_f]] : null;
    
        
        $query  = Data::list(Process::class, $search, ['date_ini'], ['organ', 'comission'], $betw, $search_obj);
        return Response()->json($query, 200);
    }

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
                'status' => PriceRecord::list_status(),
                'status_process' => Process::list_situations()
            ], 200);
        }

        //rescue comission_members
        $comissions_members = Data::list(ComissionMember::class, ['comission' => $request->search], ['responsibility']);
        return Response()->json($comissions_members);

    }
}
