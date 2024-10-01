<?php

namespace App\Http\Controllers;

use App\Models\Comission;
use App\Models\ComissionMember;
use App\Models\Dfd;
use App\Models\Process;
use App\Models\RiskMap;
use App\Models\Unit;
use App\Models\User;
use App\Utils\Notify;
use App\Utils\Utils;
use Illuminate\Http\Request;
use App\Utils\Dates;
use App\Http\Middlewares\Data;


class RiskMaps extends Controller
{
    public function __construct()
    {
        parent::__construct(RiskMap::class, User::MOD_RISKINESS['module']);
    }

    public function list(Request $request)
    {
        $date_between = [
            'date_version' => [
                $request->date_ini ?: date('Y') . '-01-01',
                $request->date_fin ?: date('Y-m-d')
            ]
        ];

        return $this->base_list(
            $request,
            ['organ', 'date_version', 'phase', 'description', 'process'],
            ['date_version'],
            ['process', 'comission'],
            $date_between
        );
    }

    /**
     * Lista processos associados a um Mapa de Risco.
     *
     * @param Request $request Dados da requisição.
     * @return \Illuminate\Http\JsonResponse Resposta JSON com a lista de processos.
     */
    public function list_processes(Request $request)
    {
        if (!$request->anyFilled('units', 'protocol', 'description', 'date_i', 'date_f')) {
            return response()->json(Notify::warning('Informe pelo menos um campo de busca...'), 500);
        }

        $search = Utils::map_search(['protocol', 'description'], $request->all());
        $betw = $request->date_i && $request->date_f ? ['date_hour_ini' => [$request->date_i, $request->date_f]] : null;
        $objs = Utils::map_search_obj($request->units, 'units', 'id');

        $query = Data::find(new Process(), $search, ['date_hour_ini'], ['organ', 'comission'], $betw, $objs);
        return response()->json($query, 200);
    }

    public function export(Request $request)
    {
        $riskmap = RiskMap::where('id', $request->id)->with([
            'process',
            'comission',
            'comission.unit',
        ])->first()->toArray();

        if (!$riskmap) {
            return Response()->json(Notify::warning('Mapa de Risco não localizado...'), 404);
        }

        return Response()->json($riskmap, 200);
    }

    public function save(Request $request)
    {
        $comission = Data::findOne(new Comission(), ['id' => $request->comission_id]);
        if (!$comission) {
            return Response()->json(Notify::warning("Comissão não existe!"), 404);
        }

        $preload = [
            'author_id' => $request->user()->id,
            'comission_members' => Data::find(new ComissionMember(), ['comission_id' => $request->comission_id])?->toArray(),
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

        if (!$request->accompaniments) {
            return Response()->json(Notify::warning("Adicione um acompanhamento para os riscos!"), 400);
        }

        if (!$request->id) {
            $last_item = RiskMap::where('process_id', '=', $request->process_id)
                ->orderByDesc('created_at')->first();
            $last_version = floatval($last_item ? $last_item->version : 0);
            $preload['version'] = sprintf("%.1f", $last_version + 1);
        }

        return $this->base_save($request, $preload);
    }

    public function details(Request $request)
    {
        return $this->base_details($request, ['process']);
    }

    public function selects(Request $request)
    {
        return response()->json(array_merge([
            'units' => Utils::map_select(Data::find(new Unit(), [], ['name'])),
            'comissions' => Utils::map_select(Data::find(new Comission(), [], ['name'])),
            'phases' => RiskMap::list_phases(),
            'risk_impacts' => RiskMap::list_impacts(),
            'risk_probabilities' => RiskMap::list_probabilities(),
            'risk_actions' => RiskMap::list_actions(),
            'process_status' => Process::list_status(),
            'responsabilitys' => ComissionMember::list_responsabilities(),
            'status' => RiskMap::list_status(),
        ], Dfd::make_details()), 200);
    }
}
