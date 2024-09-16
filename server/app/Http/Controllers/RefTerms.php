<?php

namespace App\Http\Controllers;

use App\Models\Comission;
use App\Models\ComissionMember;
use App\Models\Dfd;
use App\Models\DfdItem;
use App\Models\Etp;
use App\Models\RefTerm;
use App\Models\Unit;
use App\Models\User;
use App\Models\Process;
use App\Utils\Notify;
use App\Utils\Utils;
use Illuminate\Http\Request;
use App\Http\Middlewares\Data;

class RefTerms extends Controller
{
    /**
     * Construtor que inicializa o controller com o modelo de Setor e o módulo específico.
     */
    public function __construct()
    {
        parent::__construct(RefTerm::class, User::MOD_REFTERM['module']);
    }

    /**
     * Lista os setores com base nos critérios de filtragem.
     *
     * @param Request $request Dados da requisição para filtragem.
     * @return \Illuminate\Http\JsonResponse Resposta JSON com a lista de termos.
     */
    public function list(Request $request)
    {
        $date_between = [
            'date_hour_ini' => [
                $request->date_ini ?: date('Y') . '-01-01',
                $request->date_fin ?: date('Y-m-d')
            ]
        ];

        return $this->base_list(
            $request,
            ['protocol', 'process', 'necessity', 'type'],  // Campos para filtragem
            ['protocol'],  // Campo para ordenação
            ['comission', 'process'],  // Relações para carregamento adiantado
            $date_between
        );
    }

    /**
     * Lista processos associados a um Termo.
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

        $query = Data::find(new Process, $search, null, ['organ', 'comission'], $betw);
        return response()->json($query, 200);
    }


    /**
     * Lista itens de Termos associados a um processo.
     *
     * @param Request $request Dados da requisição.
     * @return \Illuminate\Database\Eloquent\Collection|null Resposta JSON com a lista de itens Termos.
     */
    public function list_dfd_items(Request $request)
    {
        return response()->json(
            Data::find(new DfdItem(), ['dfd' => $request->id], null, ['item', 'dotation', 'program']),
            200
        );
    }

    /**
     * Lista ETP relacionados a um processo.
     *
     * @param Request $request Dados da requisição.
     * @return \Illuminate\Database\Eloquent\Collection|null Resposta JSON com a lista de itens Termos.
     */
    public function fetch_etp(Request $request)
    {
        $etp = Data::findOne(new Etp(), ['process' => $request->process]);
        if (!$etp) {
            return response()->json(Notify::warning('O processo não possui um ETP relacionado!'), 404);
        }
        return response()->json($etp, 200);
    }

    /**
     * Fornece dados de seleção para uso em formulários ou interfaces.
     *
     * @param Request $request Dados da requisição.
     * @return \Illuminate\Http\JsonResponse Resposta JSON com dados de seleção.
     */
    public function selects(Request $request)
    {
        return response()->json(array_merge([
            'units' => Utils::map_select(Data::find(new Unit(), [], ['name'])),
            'comissions' => Utils::map_select(Data::find(new Comission(), [], ['name'])),
            'types' => RefTerm::list_types(),
            'process_status' => Process::list_status(),
            'responsibilities' => ComissionMember::list_responsabilities(),
        ], Dfd::make_details()), 200);
    }
}
