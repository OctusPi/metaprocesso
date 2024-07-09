<?php

namespace App\Http\Controllers;

use App\Models\Comission;
use App\Models\Dfd;
use App\Models\Organ;
use App\Models\Unit;
use App\Models\User;
use App\Utils\Notify;
use App\Utils\Utils;
use App\Middleware\Data;
use App\Models\Etp;
use App\Security\Guardian;
use Illuminate\Http\Request;

class Etps extends Controller
{
    public function __construct()
    {
        parent::__construct(Etp::class, User::MOD_ETPS);
        Guardian::validateAccess($this->module_id);
    }

    public function save(Request $request)
    {
        return $this->baseSave(Etp::class, array_merge($request->all(), [
            'ip' => $request->ip(),
            'user' => $this->user_loged->id
        ]));
    }

    public function list(Request $request)
    {
        return $this->baseList(
            ['protocol', 'organ', 'emission', 'comission', 'necessity', 'status'],
            ['protocol'],
            ['organ', 'comission']
        );
    }

    public function list_dfds(Request $request)
    {
        if (empty($request->all())) {
            return Response()->json(Notify::warning('Informe pelo menos um campo de busca...'), 500);
        }

        $search = Utils::map_search(['protocol', 'organ', 'description', 'unit'], $request->all());
        $betw = $request->date_i && $request->date_f ? ['date_ini' => [$request->date_i, $request->date_f]] : null;

        $query = Data::list(Dfd::class, $search, ['date_ini'], ['unit', 'comission', 'demandant', 'ordinator'], $betw);
        return Response()->json($query, 200);
    }

    public function selects(Request $request)
    {
        $comissions = $request->key && $request->key == 'organ' ? Utils::map_select(Data::list(Comission::class, [
            [
                'column' => $request->key,
                'operator' => '=',
                'value' => $request->search,
                'mode' => 'AND'
            ]
        ], ['name'])) : Utils::map_select(Data::list(Comission::class));

        $units = $request->key && $request->key == 'organ' ? Utils::map_select(Data::list(Unit::class, [
            [
                'column' => $request->key,
                'operator' => '=',
                'value' => $request->search,
                'mode' => 'AND'
            ]
        ], ['name'])) : Utils::map_select(Data::list(Unit::class));

        return Response()->json([
            'organs' => Utils::map_select(Data::list(Organ::class, order: ['name'])),
            'units' => $units,
            'status' => Etp::list_status(),
            'dfds_status' => Dfd::list_status(),
            'comissions' => $comissions,
        ], 200);
    }
}
