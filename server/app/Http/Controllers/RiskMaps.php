<?php

namespace App\Http\Controllers;

use App\Middleware\Data;
use App\Models\Comission;
use App\Models\ComissionMember;
use App\Models\Common;
use App\Models\Process;
use App\Models\RiskMap;
use App\Utils\Notify;
use App\Utils\Utils;
use Illuminate\Http\Request;

class RiskMaps extends Controller
{
    public function __construct()
    {
        parent::__construct(RiskMap::class, true, Common::MOD_RISKINESS['module']);
    }

    public function list(Request $request)
    {
        return $this->baseList(
            ['process', 'comission', 'date_version', 'phase'],
            ['date_version'],
            ['process', 'comission']
        );
    }

    public function list_processes(Request $request)
    {
        if (empty($request->except('comission'))) {
            return Response()->json(Notify::warning('Informe pelo menos um campo de busca...'), 500);
        }

        $search = Utils::map_search(['protocol', 'comission', 'description'], $request->all());
        $betw = $request->date_i && $request->date_f ? ['date_hour_ini' => [$request->date_i, $request->date_f]] : null;

        $query = Data::list(Process::class, $search, null, ['organ', 'comission'], $betw);
        return Response()->json($query, 200);
    }

    public function save(Request $request)
    {
        return $this->baseSave(array_merge($request->all(), [
            'ip' => $request->ip(),
            'user' => $request->user()->id,
            'comission_members' => ComissionMember::where('comission', $request->comission)->get()->toArray(),
        ]));
    }

    public function details(Request $request)
    {
        return $this->baseDetails($request->id, ['process', 'comission']);
    }

    public function selects(Request $request)
    {
        return Response()->json([
            'comissions' => Utils::map_select(Data::list(Comission::class, order: ['name'])),
            'phases' => RiskMap::list_phases(),
            'risk_impacts' => RiskMap::list_impacts(),
            'risk_probabilities' => RiskMap::list_probabilities(),
            'risk_actions' => RiskMap::list_actions(),
            'status_process' => Process::list_status(),
        ], 200);
    }
}
