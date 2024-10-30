<?php

namespace App\Http\Controllers;

use Str;
use Mail;
use App\Models\Organ;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Mail\ProposalRequest;
use App\Http\Middlewares\Data;
use App\Utils\{Utils, Notify};
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\{ComissionMember, Dfd, Proposal, Supplier, Unit, User, DfdItem, Process, Comission, PriceRecord};

/**
 * Controller para gerenciamento de Price Records.
 */
class PriceRecords extends Controller
{
    /**
     * Construtor do controller, inicializa a classe com o model PriceRecord.
     */
    public function __construct()
    {
        parent::__construct(PriceRecord::class, User::MOD_PRICERECORDS['module']);
    }

    /**
     * Salva o registro de preço no banco de dados.
     *
     * @param Request $request Requisição HTTP.
     * @return \Illuminate\Http\JsonResponse Resposta JSON indicando sucesso ou falha.
     */
    public function save(Request $request)
    {
        $comission_members = Data::find(new ComissionMember(), ['comission_id' => $request->comission_id]);

        if ($comission_members->isEmpty()) {
            return response()->json(Notify::warning('Comissão Selecionada não possui membros ativos...'), 400);
        }

        $save = $this->base_save($request, [
            'ip' => $request->ip(),
            'organ' => Data::getOrgan(),
            'comission_members' => $comission_members->toArray(),
            'author_id' => $request->user()->id,
            'status' => $request->status ?? PriceRecord::S_START
        ]);

        if ($save->status() == 200) {
            $this->process_collects($request);
            return response()->json(array_merge(Notify::success(''), ['instance_id' => $this->model->id]), 200);
        }

        return $save;
    }

    /**
     * Lista registros de preços.
     *
     * @param Request $request Requisição HTTP.
     * @return \Illuminate\Http\JsonResponse Lista de registros de preços.
     */
    public function list(Request $request)
    {
        $date_between = [
            'date_ini' => [
                $request->date_ini ?: (date('Y') - 1) . '-01-01',
                $request->date_fin ?: (date('Y') + 1) . '-12-31'
            ]
        ];

        $objs_search = Utils::map_search_obj($request->suppliers, 'suppliers', 'id');

        return $this->base_list(
            $request,
            ['protocol', 'status'],
            ['date_ini', 'desc'],
            ['process'],
            $date_between,
            $objs_search
        );
    }

    /**
     * Retorna detalhes de um registro de preço.
     *
     * @param Request $request Requisição HTTP.
     * @return \Illuminate\Http\JsonResponse Detalhes do registro.
     */
    public function details(Request $request)
    {
        $pricerecord = Data::findOne(new PriceRecord(), ['id' => $request->id], null, ['organ', 'process']);

        if ($pricerecord) {
            return response()->json($pricerecord);
        }

        return response()->json(Notify::warning('Registro não localizado'), 404);
    }

    /**
     * Retorna dados para export
     *
     * @param Request $request Requisição HTTP.
     * @return \Illuminate\Http\JsonResponse Detalhes do registro.
     */
    public function export(Request $request)
    {
        $pricerecord = Data::findOne(new PriceRecord(), ['id' => $request->id]);
        $proposals = Data::find(new Proposal(), ['pricerecord_id' => $request->id], ['date_fin'], ['supplier']);
        $process = Data::findOne(new Process(), ['id' => $pricerecord->process_id]);
        $items   = (new ProposalsSupplier())->dfdItems($process?->dfds);

        if ($pricerecord) {
            return response()->json([
                'process' => $process,
                'pricerecord' => $pricerecord,
                'proposals' => $proposals,
                'items' => $items
            ]);
        }

        return response()->json(Notify::warning('Registro não localizado'), 404);
    }

    /**
     * Lista processos.
     *
     * @param Request $request Requisição HTTP.
     * @return \Illuminate\Http\JsonResponse Lista de processos.
     */
    public function list_processes(Request $request)
    {
        if (!$request->anyFilled('protocol', 'description', 'date_i', 'date_f', 'units')) {
            return response()->json(Notify::warning('Informe pelo menos um campo de busca...'), 500);
        }

        $search = Utils::map_search(['protocol', 'description'], $request->all());
        $betw = $request->date_i && $request->date_f ? ['date_hour_ini' => [$request->date_i, $request->date_f]] : null;
        $objs = Utils::map_search_obj($request->units, 'units', 'id');

        $query = Data::find(new Process(), $search, ['date_hour_ini'], ['organ'], $betw, $objs);

        return response()->json($query);
    }

    /**
     * Lista itens de DFD associados a um processo.
     *
     * @param Request $request Requisição HTTP.
     * @return \Illuminate\Http\JsonResponse Lista de itens DFD.
     */
    public function list_dfd_items(Request $request)
    {
        return response()->json(Data::find(new DfdItem(), ['dfd_id' => $request->id], null, ['item', 'dotation', 'program']));
    }

    public function list_grouped_items(Request $request)
    {
        $process = Data::findOne(new Process(), ['id' => $request->process_id]);
        if (!is_null($process)) {
            return response()->json((new ProposalsSupplier())->dfdItems($process->dfds), 200);
        }

        return response()->json(Notify::warning('Processo não localizado...'), 404);
    }

    /**
     * Lista fornecedores associados a uma coleta de preços.
     *
     * @param Request $request Requisição HTTP.
     * @return \Illuminate\Http\JsonResponse Lista de fornecedores.
     */
    public function list_suppliers(Request $request)
    {
        return response()->json(Data::find(new Supplier(), [
            ['column' => 'name', 'operator' => 'LIKE', 'value' => "%$request->supplier%", 'mode' => 'OR'],
            ['column' => 'cnpj', 'operator' => 'LIKE', 'value' => "%$request->supplier%", 'mode' => 'OR']
        ], ['name']));
    }

    /**
     * Retorna seleções de dados para uso em formulários ou filtros.
     *
     * @param Request $request Requisição HTTP.
     * @return \Illuminate\Http\JsonResponse Seleções de dados.
     */
    public function selects(Request $request)
    {
        return response()->json(array_merge([
            'comissions' => Utils::map_select(Data::find(new Comission(), ['status' => Comission::STATUS_ACTIVE], ['name'])),
            'comission_responsibilitys' => ComissionMember::list_responsabilities(),
            'units' => Utils::map_select(Data::find(new Unit(), [], ['name'])),
            'status' => PriceRecord::list_status(),
            'calctypes' =>PriceRecord::list_calctypes(),
            'process_modalities' => Process::list_modalitys(),
            'process_types' => Process::list_types(),
            'process_status' => Process::list_status(),
            'suppliers' => Data::find(new Supplier(), order: ['name']),
            'supplier_modalities' => Supplier::list_modalitys(),
            'supplier_sizes' => Supplier::list_sizes(),
            'proposal_modalities' => Proposal::list_modalitys(),
            'proposal_status' => Proposal::list_status()
        ], Dfd::make_details()), 200);
    }

    /**
     * Reenvia uma solicitação de coleta para o fornecedor.
     *
     * Esta função busca uma proposta (`Proposal`) no banco de dados com base no ID fornecido no `Request`.
     * Se a proposta for encontrada e não tiver status finalizado, é enviado um email para o fornecedor
     * solicitando a coleta de dados, utilizando a classe `ProposalRequest`.
     *
     * @param \Illuminate\Http\Request $request O objeto de requisição contendo os dados, incluindo o ID da proposta.
     * @return \Illuminate\Http\JsonResponse Retorna uma resposta JSON contendo uma mensagem de notificação e o status HTTP.
     *
     * Status possíveis:
     * - 404: Proposta não encontrada.
     * - 400: Proposta já foi finalizada pelo fornecedor.
     * - 200: Solicitação de coleta reenviada com sucesso.
     * - 500: Falha ao reenviar a solicitação de coleta.
     */
    public function send_collect(Request $request)
    {
        $proposal = Data::findOne(new Proposal(), ['id' => $request->id], null, ['organ', 'process', 'supplier', 'pricerecord']);

        if (is_null($proposal)) {
            return response()->json(Notify::warning('Coleta não localizada...'), 404);
        }

        if ($proposal->status == Proposal::S_FINISHED) {
            return response()->json(Notify::warning('Coleta já preenchida pelo fornecedor...'), 400);
        }

        $send = Mail::to($proposal->supplier->email)->send(new ProposalRequest(
            $proposal->organ?->name,
            $proposal->process,
            $proposal->supplier,
            $proposal->token,
            $proposal->protocol,
            $proposal->date_ini . ' ' . $proposal->hour_ini,
            $proposal->pricerecord->date_fin
        ));

        $code = !is_null($send) ? 200 : 500;
        $msg = !is_null($send) ? Notify::success("Solicitação de coleta reenviada...") : Notify::error('Falha ao reenviar solicitação...');

        return response()->json($msg, $code);
    }

    /**
     * Consulta e filtra preços de itens de licitação da API do TCE.
     *
     * Esta função faz uma requisição GET para a URL configurada da API do TCE, utilizando os parâmetros de origem e ano fornecidos.
     * O resultado é filtrado com base no item fornecido na requisição, comparando a descrição dos itens de licitação.
     *
     * @param \Illuminate\Http\Request $request O objeto de requisição contendo os seguintes dados:
     * - `year` (string): O ano a ser consultado, usado para definir o intervalo de datas.
     * - `origin` (string): O parâmetro de origem para a consulta.
     * - `item` (string): Um JSON que contém os dados do item para filtrar os resultados da API.
     *
     * @return \Illuminate\Http\JsonResponse Retorna uma resposta JSON com os itens filtrados. Se não houver itens correspondentes, retorna um array vazio.
     *
     * Em caso de falha na requisição, retorna:
     * - 404: Se houver um erro ao consultar a API ou se a consulta falhar.
     *
     * @throws \Exception Lança exceções caso a requisição à API falhe.
     */
    public function prices_tce(Request $request)
    {
        $year = "$request->year-01-01_$request->year-12-31";
        $tce_url = Utils::map_params(config('app.tce_url'), ['origin' => $request->origin, 'year' => $year]);

        try {
            $client = new Client();
            $resp = $client->get($tce_url, [
                'headers' => [
                    'Access-Control-Allow-Origin' => '*',
                    'Content-Type' => 'application/json'
                ]
            ]);

            $data = json_decode($resp->getBody(), true);
            $search_item = json_decode($request->item, true);

            $filter_data = array_map(function ($item) use ($search_item) {
                // Filtrar os itens que atendem à condição
                return array_values(array_filter($item, function ($v) use ($search_item) {
                    return strpos(strtolower(Utils::normalize_string($v['descricao_item_licitacao'])), strtolower(Utils::normalize_string(explode(' ', $search_item['item']['name'])[0]))) !== false;
                }));
            }, $data);

            // Remover arrays vazios do resultado final e reindexar o array principal
            $filter_data = array_values(array_filter($filter_data));

            return response()->json($filter_data[0] ?? [], 200);

        } catch (\Exception $e) {
            Log::alert('Falha ao receber dados da API: ' . $e->getMessage());
            return response()->json(Notify::warning('Falha ao receber dados da API'), 404);
        }
    }

    public function prices_pncp(Request $request)
    {
        return response()->json(Notify::info('Consulta PNCP Indisponível no momento.'), 500);
    }

    /**
     * Processa as propostas associadas ao registro de preço.
     *
     * @param Request $request Requisição HTTP.
     * @return void
     */
    private function process_collects(Request $request): void
    {
        $pricerecord = $request->id ?? $this->model->id;

        if ($pricerecord) {
            $suppliers = json_decode($request->suppliers);
            $process = json_decode($request->process);
            $hour_send = date('H:i:s');

            // create proposals by e-mail request
            if (!empty($suppliers)) {
                foreach ($suppliers as $supplier) {
                    $token = $pricerecord . $supplier->id . Str::random(16);

                    if (
                        !Proposal::firstWhere([
                            ['pricerecord_id', $pricerecord],
                            ['supplier_id', $supplier->id]
                        ])
                    ) {
                        $proposal = new Proposal([
                            'protocol' => $request->protocol,
                            'ip' => $request->ip(),
                            'token' => $token,
                            'date_ini' => $request->date_ini,
                            'hour_ini' => $hour_send,
                            'organ_id' => Data::getOrgan(),
                            'process_id' => $process->id,
                            'pricerecord_id' => $pricerecord,
                            'supplier_id' => $supplier->id,
                            'author_id' => $request->user()->id,
                            'modality' => Proposal::M_MAIL,
                            'status' => Proposal::S_START
                        ]);

                        $organ = Organ::find(Data::getOrgan());

                        if ($proposal->save()) {
                            Mail::to($supplier->email)->send(new ProposalRequest(
                                $organ?->name,
                                $process,
                                $supplier,
                                $token,
                                $request->protocol,
                                $request->date_ini . ' ' . $hour_send,
                                $request->date_fin
                            ));
                        }
                    }
                }
            }

            // create proposals by manual serach prices
            if ($request->manual_items) {

                $manual_items = json_decode($request->manual_items, true);
                $global = array_reduce($manual_items ?? [], function ($carry, $item) {
                    return $carry += round(Utils::toFloat($item['value'] ?? 0) * $item['quantity'] ?? 0, 2);
                });
                $proposal_status = array_filter($manual_items, function ($obj) {
                    return isset($obj['value']);
                }, 0);

                if($request->manual_proposal_id){
                    $proposal = Proposal::find($request->manual_proposal_id);
                    $proposal->update(['items' => $manual_items, 'global'=>$global, 'status' => count($proposal_status) < count($manual_items) ? Proposal::S_PENDING : Proposal::S_FINISHED]);
                }else{
                    Proposal::create([
                        'protocol' => $request->protocol,
                        'ip' => $request->ip(),
                        'token' => $pricerecord . '-00'.Proposal::M_MANUAL.'-' . Str::random(16),
                        'date_ini' => $request->date_ini,
                        'hour_ini' => $hour_send,
                        'date_fin' => date('d/m/Y'),
                        'hour_fin' => date('H:i:s'),
                        'organ_id' => Data::getOrgan(),
                        'process_id' => $process->id,
                        'pricerecord_id' => $pricerecord,
                        'supplier_id' => null,
                        'author_id' => $request->user()->id,
                        'items' => $manual_items,
                        'global'=>$global,
                        'modality' => Proposal::M_MANUAL,
                        'status' => count($proposal_status) < count($manual_items) ? Proposal::S_PENDING : Proposal::S_FINISHED,
                    ]);
                }
            }

            self::updateStatusPriceRecord($pricerecord);
        }
    }

    /**
     * Atualiza o status do registro de preço com base nas propostas associadas.
     * O status será atualizado para S_FINISHED apenas se todas as propostas tiverem esse status.
     *
     * @param int $id
     * @return void
     */
    public static function updateStatusPriceRecord(int $id): void
    {
        // Obtém todas as propostas relacionadas ao registro de preço
        $proposals = Proposal::where('pricerecord_id', $id)->get();

        if (!$proposals->isEmpty()) {
            // Verifica se todas as propostas têm o status S_FINISHED
            $allFinished = $proposals->every(function ($proposal) {
                return $proposal->status == Proposal::S_FINISHED;
            });

            // Atualiza o status do PriceRecord com base nas propostas
            $pricerecord = PriceRecord::find($id);
            if ($pricerecord) {
                $pricerecord->update([
                    'status' => $allFinished ? PriceRecord::S_FINISHED : PriceRecord::S_PENDING
                ]);
            }
        }
    }

}
