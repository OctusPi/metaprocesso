<?php

namespace App\Http\Controllers;

use App\Models\{CatalogItem, Comission, ComissionMember, Dfd, DfdItem, Etp, Organ, PriceRecord, Process, User};
use App\Utils\Utils;
use App\Utils\Notify;
use Illuminate\Http\Request;
use App\Http\Middlewares\Data;

class Etps extends Controller
{
    /**
     * Construtor para inicializar o controller de Etps com o modelo Etp.
     */
    public function __construct()
    {
        parent::__construct(Etp::class, User::MOD_ETPS['module']);
    }

    /**
     * Salva um novo registro de Etp ou atualiza um existente.
     *
     * @param Request $request Dados da requisição.
     * @return \Illuminate\Http\JsonResponse Resposta JSON com o resultado da operação.
     */
    public function save(Request $request)
    {
        return $this->base_save($request, [
            'ip' => $request->ip(),
            'user' => $request->user()->id,
        ]);
    }

    /**
     * Lista os Etps com base em critérios de filtragem.
     *
     * @param Request $request Dados da requisição para filtragem.
     * @return \Illuminate\Http\JsonResponse Resposta JSON com a lista de Etps.
     */
    public function list(Request $request)
    {
        return $this->base_list(
            $request,
            ['protocol', 'organ', 'emission', 'comission', 'necessity', 'status'],
            ['protocol'],
            ['organ', 'comission', 'process']
        );
    }

    /**
     * Retorna os detalhes de um Etp específico.
     *
     * @param Request $request Dados da requisição.
     * @return \Illuminate\Http\JsonResponse Detalhes do Etp solicitado.
     */
    public function details(Request $request)
    {
        return $this->base_details($request, ['process', 'process.organ']);
    }

    /**
     * Lista processos associados a um Etp.
     *
     * @param Request $request Dados da requisição.
     * @return \Illuminate\Http\JsonResponse Resposta JSON com a lista de processos.
     */
    public function list_processes(Request $request)
    {
        if (empty($request->except('organ', 'comission'))) {
            return response()->json(Notify::warning('Informe pelo menos um campo de busca...'), 500);
        }

        $search = Utils::map_search(['protocol', 'organ', 'comission', 'description'], $request->all());
        $betw = $request->date_i && $request->date_f ? ['date_hour_ini' => [$request->date_i, $request->date_f]] : null;

        $query = Data::find(new Process(), $search, null, ['organ', 'comission'], $betw);
        return response()->json($query, 200);
    }

    /**
     * Lista itens de DFD associados a um processo.
     *
     * @param Request $request Dados da requisição.
     * @return \Illuminate\Database\Eloquent\Collection|null Resposta JSON com a lista de itens DFD.
     */
    public function list_dfd_items(Request $request)
    {
        return Data::find(new DfdItem(), ['dfd' => $request->id], null, ['item', 'dotation', 'program']);
    }

    /**
     * Fornece seleções de dados para uso em formulários ou interfaces, baseado em parâmetros específicos.
     *
     * @param Request $request Dados da requisição.
     * @return \Illuminate\Http\JsonResponse Resposta JSON com dados de seleção.
     */
    public function selects(Request $request)
    {
        if ($request->key == 'comission') {
            return response()->json(
                Data::find(
                    new ComissionMember(),
                    ['comission' => $request->search],
                    ['responsibility']
                )
            );
        }

        return response()->json([
            'organs' => Utils::map_select(Data::find(new Organ(), [], ['name'])),
            'comissions' => Utils::map_select(Data::find(new Comission(), [], ['name'])),
            // Additional selection data...
        ], 200);
    }
}
