<?php

namespace App\Http\Controllers;

use App\Models\{Comission, ComissionMember, Dfd, DfdItem, Etp, Process, User};
use App\Models\Attachment;
use App\Models\EtpPartial;
use App\Models\Unit;
use App\Utils\Utils;
use App\Utils\Notify;
use Illuminate\Http\Request;
use App\Http\Middlewares\Data;

class Etps extends Controller
{
    /**
     * Construtor para inicializar o controller de Etps com o modelo ETP.
     */
    public function __construct()
    {
        parent::__construct(Etp::class, User::MOD_ETPS['module']);
    }

    /**
     * Salva um novo registro de ETP ou atualiza um existente.
     *
     * @param Request $request Dados da requisição.
     * @return \Illuminate\Http\JsonResponse Resposta JSON com o resultado da operação.
     */
    public function save(Request $request)
    {
        return $this->base_save($request, [
            'ip' => $request->ip(),
            'author_id' => $request->user()->id,
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
        $date_between = [
            'emission' => [
                $request->date_ini ?: (date('Y') - 1) . '-01-01',
                $request->date_fin ?: (date('Y') + 1) . '-12-31',
            ]
        ];

        return $this->base_list(
            $request,
            ['protocol', 'necessity', 'status'],
            ['emission', 'desc'],
            ['comission', 'process'],
            $date_between
        );
    }

    /**
     * Retorna os detalhes de um ETP específico.
     *
     * @param Request $request Dados da requisição.
     * @return \Illuminate\Http\JsonResponse Detalhes do ETP solicitado.
     */
    public function details(Request $request)
    {
        return $this->base_details($request, ['process']);
    }

    /**
     * Lista processos associados a um ETP.
     *
     * @param Request $request Dados da requisição.
     * @return \Illuminate\Http\JsonResponse Resposta JSON com a lista de processos.
     */
    public function list_processes(Request $request)
    {
        if (!$request->anyFilled('protocol', 'description', 'date_i', 'date_f', 'units')) {
            return response()->json(Notify::warning('Informe pelo menos um campo de busca...'), 500);
        }

        $search = Utils::map_search(['protocol', 'description'], $request->all());
        $betw = $request->date_i && $request->date_f ? ['date_hour_ini' => [$request->date_i, $request->date_f]] : null;
        $objs = Utils::map_search_obj($request->units, 'units', 'id');

        $query = Data::find(new Process(), $search, null, ['comission'], $betw, $objs);
        return response()->json($query, 200);
    }

    /**
     * Recupera os dados do ETP para exportar.
     *
     * @param Request $request Dados da requisição incluindo o ID do ETP.
     * @return \Illuminate\Http\JsonResponse Resposta JSON com detalhes do ETP.
     */
    public function export(Request $request)
    {
        $etp = $this->base_details($request, ['process', 'comission', 'author']);

        if ($etp->status() == 200) {
            $data = $etp->getData(true);

            $attachments = Data::find(
                new Attachment(),
                ['origin' => Attachment::ETP, 'protocol' => $data['protocol']],
                ['updated_at']
            );

            return response()->json(array_merge($data, ['attachments' => $attachments->toArray()]), 200);
        }

        return response()->json(Notify::warning("ETP não localizado..."), $etp->status());
    }

    /**
     * Lista itens de ETP associados a um processo.
     *
     * @param Request $request Dados da requisição.
     * @return \Illuminate\Database\Eloquent\Collection|null Resposta JSON com a lista de itens ETP.
     */
    public function list_dfd_items(Request $request)
    {
        return response()->json(
            Data::find(new DfdItem(), ['dfd_id' => $request->id], null, ['item', 'dotation', 'program']),
            200
        );
    }


    /**
     * Retorna os dados do Processo selecionado.
     *
     * @param Request $request Dados da requisição.
     * @return \Illuminate\Database\Eloquent\Collection|null Resposta JSON com a lista de itens Termos.
     */
    public function fetch_process(Request $request)
    {
        $process = Data::findOne(new Process(), ['id' => $request->process_id], with: ['etp']);

        if (!$process) {
            return response()->json(Notify::warning('O processo não existe!'), 404);
        }

        if ($process->etp && $process->etp->id != $request->id) {
            return response()->json(Notify::warning('O processo já possui um ETP!'), 401);
        }

        return response()->json($process, 200);
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

        return response()->json(array_merge([
            'units' => Utils::map_select(Data::find(new Unit(), [], ['name'])),
            'comissions' => Utils::map_select(Data::find(new Comission(), ['status' => Comission::STATUS_ACTIVE], ['name'])),
            'process_status' => Process::list_status(),
            'status' => ETP::list_status(),
            'responsibilities' => ComissionMember::list_responsabilities(),
            'installment_types' => Process::list_installments_types(),
            'vars' => [
                'ORIGIN_ETP' => User::MOD_ETPS['id']
            ]
        ], Dfd::make_details()), 200);
    }
}
