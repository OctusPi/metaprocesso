<?php

namespace App\Http\Controllers;

use App\Mail\ProposalRequest;
use App\Models\{ ComissionMember, Dfd, Proposal, Supplier, Unit, User, DfdItem, Process, Comission, PriceRecord };
use App\Utils\{Utils, Notify};
use Illuminate\Http\Request;
use App\Http\Middlewares\Data;
use App\Http\Controllers\Controller;
use Mail;
use Str;

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
                $request->date_ini ?: (date('Y')-1) . '-01-01',
                $request->date_fin ?: (date('Y')+1) . '-12-31'
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
            'comissions' => Utils::map_select(Data::find(new Comission(), [], ['name'])),
            'units' => Utils::map_select(Data::find(new Unit(), [], ['name'])),
            'status' => PriceRecord::list_status(),
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

    public function send_collect(Request $request){
        $proposal = Data::findOne(new Proposal(), ['id' => $request->id]);

        if(is_null($proposal)){
            return response()->json(Notify::warning('Coleta não localizada...'), 404);
        }

        if($proposal->status == Proposal::S_FINISHED){
            return response()->json(Notify::warning('Coleta já preenchida pelo fornecedor...'), 400);
        }

        $process  = json_decode(json_encode(Process::with('organ')->find($proposal->process)?->toArray()));
        $supplier  = json_decode(json_encode(Supplier::find($proposal->supplier)?->toArray()));
        $price_rec = json_decode(json_encode(PriceRecord::find($proposal->pricerecord)?->toArray()));

        $send = Mail::to($supplier->email)->send(new ProposalRequest(
            $process,
            $supplier,
            $proposal->token,
            $proposal->protocol,
            $proposal->date_ini.' '.$proposal->hour_ini,
            $price_rec->date_fin
        ));

        $code = !is_null($send) ? 200 : 500;
        $msg  = !is_null($send) ? Notify::success("Solicitação de coleta reenviada...") : Notify::error('Falha ao reenviar solicitação...');

        return response()->json($msg, $code);
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

            if (!empty($suppliers)) {
                foreach ($suppliers as $supplier) {
                    $token = $pricerecord . $supplier->id . Str::random(16);
                    $hour_send = date('H:i:s');

                    if (!Proposal::firstWhere([
                        ['pricerecord_id', $pricerecord],
                        ['supplier_id', $supplier->id]
                    ])) {
                        $proposal = new Proposal([
                            'protocol' => $request->protocol,
                            'ip' => $request->ip(),
                            'author_id' => $request->user()->id,
                            'token' => $token,
                            'date_ini' => $request->date_ini,
                            'hour_ini' => $hour_send,
                            'organ_id' => Data::getOrgan(),
                            'process_id' => $process->id,
                            'pricerecord_id' => $pricerecord,
                            'supplier_id' => $supplier->id,
                            'modality' => Proposal::M_MAIL,
                            'status' => Proposal::S_START
                        ]);

                        if ($proposal->save()) {
                            Mail::to($supplier->email)->send(new ProposalRequest(
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
        }
    }
}
