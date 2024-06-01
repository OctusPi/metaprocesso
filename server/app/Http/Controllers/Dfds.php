<?php

namespace App\Http\Controllers;

use App\Models\Comission;
use App\Models\Demandant;
use App\Models\Dfd;
use App\Models\Ordinator;
use App\Models\Unit;
use App\Models\User;
use App\Utils\Utils;
use App\Models\Organ;
use App\Middleware\Data;
use App\Security\Guardian;
use Illuminate\Http\Request;

class Dfds extends Controller
{
    public function __construct()
    {
        parent::__construct(User::MOD_MANAGEMENT);
        Guardian::validateAccess($this->module_id);
    }

    public function save(Request $request)
    {
        return $this->baseSave(Dfd::class, $request->all());
    }

    public function update(Request $request)
    {
        return $this->baseUpdate(Dfd::class, $request->id, $request->all());
    }

    public function delete(Request $request)
    {
        return $this->baseDelete(Dfd::class, $request->id, $request->password);
    }

    public function list(Request $request)
    {
        $search = ['organ', 'unit', 'name'];
        return $this->baseList(Dfd::class, $search, $request->all(), ['name'], ['organ', 'unit']);
    }

    public function details(Request $request)
    {
        return $this->baseDetails(Dfd::class, $request->id);
    }

    public function selects(Request $request)
    {
        $units      = [];
        $ordinators = [];
        $demandants = [];
        $comissions = [];
        
        if($request->key){
            $units = $request->key == 'organ' ? Utils::map_select(Data::list(Unit::class, [
                [
                    'column'   => $request->key,
                    'operator' => '=',
                    'value'    => $request->search,
                    'mode'     => 'AND'
                ]
                ], ['name'])) : Utils::map_select(Data::list(Unit::class));

            $ordinators = Utils::map_select(Data::list(Ordinator::class, [
                [
                    'column'   => $request->key,
                    'operator' => '=',
                    'value'    => $request->search,
                    'mode'     => 'AND'
                ]
                ], ['name']));
            
            $demandants = Utils::map_select(Data::list(Demandant::class, [
                [
                    'column'   => $request->key,
                    'operator' => '=',
                    'value'    => $request->search,
                    'mode'     => 'AND'
                ]
                ], ['name']));

            $comissions = Utils::map_select(Data::list(Comission::class, [
                [
                    'column'   => $request->key,
                    'operator' => '=',
                    'value'    => $request->search,
                    'mode'     => 'AND'
                ]
                ], ['name']));
        }
       

        return Response()->json([
            'organs' => Utils::map_select(Data::list(Organ::class, order:['name'])),
            'units'  => $units,
            'ordinators' => $ordinators,
            'demandants' => $demandants,
            'comissions' => $comissions,
            'prioritys' => Dfd::list_priority(),
            'hirings' => Dfd::list_hirings(),
            'acquisitions' => Dfd::list_acquisitions(),
            'bonds' => Dfd::list_bonds()
        ], 200);
    }
}
