<?php

namespace App\Http\Controllers;

use App\Middleware\Data;
use App\Models\Comission;
use App\Models\ComissionMember;
use App\Models\Dfd;
use App\Models\DfdItem;
use App\Models\Dotation;
use App\Models\Organ;
use App\Models\PriceRecord;
use App\Models\Process;
use App\Models\Program;
use App\Models\Supplier;
use App\Models\Unit;
use App\Utils\Notify;
use App\Utils\Utils;
use Illuminate\Http\Request;

class PriceRecords extends Controller
{
    
    public function list_processes(Request $request){

        if(empty($request->all())){
            return Response()->json(Notify::warning('Informe pelo menos um campo de busca...'), 500);
        }

        $search = Utils::map_search(['protocol', 'organ', 'description'], $request->all());
        $search_obj = Utils::map_search_obj($request->units, 'units', 'id');
        $betw       = $request->date_i && $request->date_f ? ['date_hour_ini' => [$request->date_i, $request->date_f]] : null;
    
        
        $query  = Data::list(Process::class, $search, ['date_hour_ini'], ['organ', 'comission'], $betw, $search_obj);
        return Response()->json($query, 200);
    
    }

    public function list_suppliers(Request $request){

        if(is_null($request->data)){
            return Response()->json(Notify::warning('Dados de busca não informados...'), 401);
        }


        $suppliers = Supplier::where('cnpj', 'LIKE', "%{$request->data}%")
        ->orWhere('name', 'LIKE', "%{$request->data}%")
        ->orWhere('agent', 'LIKE', "%{$request->data}%")
        ->get();
        
        return Response()->json($suppliers ?? []);
    
    }

    public function list_dfd_items(Request $request){
        return Data::list(DfdItem::class, ['dfd' => $request->id], null, ['item']);
    }

    public function selects(Request $request)
    {
        
        if ($request->key != 'comission') {
            
            $units = [];
            $comissions = [];
            $programs = [];
            $dotations = [];

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

            $programs = Utils::map_select(Data::list(Program::class, [
                [
                    'column' => $request->key,
                    'operator' => '=',
                    'value' => $request->search,
                    'mode' => 'AND'
                ]
            ], ['name']));

            $dotations = Utils::map_select(Data::list(Dotation::class, [
                [
                    'column' => $request->key,
                    'operator' => '=',
                    'value' => $request->search,
                    'mode' => 'AND'
                ]
            ], ['name']));

            return Response()->json([
                'organs' => Utils::map_select(Data::list(Organ::class, order: ['name'])),
                'units' => $units,
                'comissions' => $comissions,
                'prioritys_dfd' => Dfd::list_priority(),
                'hirings_dfd' => Dfd::list_hirings(),
                'acquisitions_dfd' => Dfd::list_acquisitions(),
                'status' => PriceRecord::list_status(),
                'status_process' => Process::list_status(),
                'status_dfds' => Dfd::list_status(),
                'programs' => $programs,
                'dotations' => $dotations,
                'responsibilitys' => ComissionMember::list_responsabilities()
            ], 200);
        }

        //rescue comission_members
        $comissions_members = Data::list(ComissionMember::class, ['comission' => $request->search], ['responsibility']);
        return Response()->json($comissions_members);
        
    }
}
