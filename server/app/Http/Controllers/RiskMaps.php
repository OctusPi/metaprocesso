<?php

namespace App\Http\Controllers;

use App\Middleware\Data;
use App\Models\Comission;
use App\Models\ComissionMember;
use App\Models\Common;
use App\Models\Process;
use App\Models\RiskMap;
use App\Models\Unit;
use App\Utils\Notify;
use App\Utils\Utils;
use Illuminate\Http\Request;
use App\Utils\Dates;

class RiskMaps extends Controller
{
    public function __construct()
    {
        parent::__construct(RiskMap::class, true, Common::MOD_RISKINESS['module']);
    }

    public function list(Request $request)
    {
        return $this->baseList(
            ['comission', 'date_version', 'phase', 'description'],
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
        $preload = [
            'author' => $request->user()->id,
            'comission_members' => ComissionMember::where('comission', $request->comission)->get()->toArray(),
            'date_version' => Dates::nowWithFormat(Dates::PTBR),
        ];

        $riskiness = collect(json_decode($request->riskiness));
        if ($riskiness->isEmpty()) {
            return Response()->json(Notify::warning("Ao menos um risco deve ser especificado!"), 400);
        }

        $empty = $riskiness->firstWhere('risk_actions', []);
        if ($empty) {
            return Response()->json(Notify::warning("Adicione ao menos uma ação para o risco $empty->verb_id!"), 400);
        }

        if (!$request->id) {
            $last_item = RiskMap::orderByDesc('created_at')->first();
            $last_version = floatval($last_item ? $last_item->version : 0);
            $preload['version'] = sprintf("%.1f", $last_version + 1);
        }

        return $this->baseSave(array_merge($request->all(), $preload));
    }

    public function details(Request $request)
    {
        return $this->baseDetails($request->id, ['process']);
    }

    public function selects(Request $request)
    {
        $units = $request->key ? Utils::map_select(Data::list(Unit::class, [
            [
                'column' => $request->key,
                'operator' => '=',
                'value' => $request->search,
                'mode' => 'AND'
            ]
        ], ['name'])) : Utils::map_select(Data::list(Unit::class));

        $comissions = $request->key ? Utils::map_select(Data::list(Comission::class, [
            [
                'column' => $request->key,
                'operator' => '=',
                'value' => $request->search,
                'mode' => 'AND'
            ]
        ], ['name'])) : Utils::map_select(Data::list(Comission::class));

        return Response()->json([
            'units' => $units,
            'comissions' => $comissions,
            'organs' => Utils::map_select(Data::list(Comission::class, order: ['name'])),
            'phases' => RiskMap::list_phases(),
            'risk_impacts' => RiskMap::list_impacts(),
            'risk_probabilities' => RiskMap::list_probabilities(),
            'risk_actions' => RiskMap::list_actions(),
            'status_process' => Process::list_status(),
        ], 200);
    }
}
