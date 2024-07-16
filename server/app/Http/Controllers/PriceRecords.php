<?php

namespace App\Http\Controllers;

use App\Models\Dfd;
use App\Models\Unit;
use App\Utils\Utils;
use App\Models\Organ;
use App\Utils\Notify;
use App\Models\Common;
use App\Models\DfdItem;
use App\Models\Process;
use App\Models\Program;
use App\Middleware\Data;
use App\Models\Dotation;
use App\Models\Supplier;
use App\Models\Comission;
use App\Models\PriceRecord;
use Illuminate\Http\Request;
use App\Models\ComissionMember;

class PriceRecords extends Controller
{

    public function __construct()
    {
        parent::__construct(PriceRecord::class, true, Common::MOD_PRICERECORDS['module']);
    }
    
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

        //rescue comission_members
        if($request->key == 'comission'){
            $comissions_members = Data::list(ComissionMember::class, ['comission' => $request->search], ['responsibility']);
            return Response()->json($comissions_members);
        }

        $generic = [
            'organs' => Utils::map_select(Data::list(Organ::class, order: ['name'])),
            'prioritys_dfd' => Dfd::list_priority(),
            'hirings_dfd' => Dfd::list_hirings(),
            'acquisitions_dfd' => Dfd::list_acquisitions(),
            'status' => PriceRecord::list_status(),
            'status_process' => Process::list_status(),
            'modalitys_suppliers' => Supplier::list_modalitys(),
            'sizes_suppliers' => Supplier::list_sizes(),
            'status_dfds' => Dfd::list_status(),
            'responsibilitys' => ComissionMember::list_responsabilities()
        ];

        // rescue organs
        if(is_null($request->key)){
            return Response()->json($generic);
        }  
        
        // rescues other seclects by organ or unit
        if ($request->key == 'organ' || $request->key == 'unit') {
            
            $units = $request->key == 'organ' 
            ? Utils::map_select(Data::list(Unit::class, [$request->key => $request->search], ['name'])) 
            : Utils::map_select(Data::list(Unit::class));

            $comissions = Utils::map_select(Data::list(Comission::class, [$request->key => $request->search], ['name']));

            $programs = Utils::map_select(Data::list(Program::class, [$request->key => $request->search], ['name']));

            $dotations = Utils::map_select(Data::list(Dotation::class, [$request->key => $request->search], ['name']));

            $especializer = array_merge($generic, [
                'units' => $units,
                'comissions' => $comissions,
                'programs' => $programs,
                'dotations' => $dotations
            ]);


            return Response()->json($especializer);
        }
        
        return Response()->json(Notify::warning("Selects não localizados..."), 404);
    }
}
