<?php

namespace App\Http\Controllers;

use App\Mail\ProposalRequest;
use App\Models\ComissionMember;
use App\Models\Dfd;
use App\Models\Proposal;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use App\Utils\Utils;
use App\Utils\Notify;
use App\Models\DfdItem;
use App\Models\Process;
use App\Models\Comission;
use App\Models\PriceRecord;
use Illuminate\Http\Request;
use App\Http\Middlewares\Data;
use App\Http\Controllers\Controller;
use Mail;
use Str;

class PriceRecords extends Controller
{
    public function __construct()
    {
        parent::__construct(PriceRecord::class, User::MOD_PRICERECORDS['module']);
    }

    public function save(Request $request)
    {
        $comission_members = Data::find(new ComissionMember(), ['comission' => $request->comission]);
        if ($comission_members->count() == 0) {
            return response()->json(Notify::warning('Comissão Selecionada não possui membros ativos...'), 400);
        }

        $process = json_decode($request->process);

        $save = $this->base_save($request, [
            'ip' => $request->ip(),
            'process' => $process->id,
            'organ' => Data::getOrgan(),
            'comission_members' => $comission_members->toArray(),
            'author' => $request->user()->id,
            'status' => $request->status ?? PriceRecord::S_START
        ]);

        if($save->status() == 200){
            $this->processProposals($request);
        }

        return $save;
    }

    public function list(Request $request)
    {
        $date_between = $request->has(['date_ini', 'date_fin']) ?
                        ['date_ini' => [$request->date_ini, $request->date_fin]] :
                        ['date_ini' => [date('Y') . '-01-01', date('Y-m-d')]];

        $objs_search = Utils::map_search_obj($request->suppliers, 'suppliers', 'id');

        return $this->base_list(
            $request,
            ['protocol', 'status'],
            ['date_ini'],
            ['process'],
            $date_between,
            $objs_search
        );
    }

    /**
     * Lista processos associados a um Etp.
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

        $query = Data::find(new Process(), $search, null, ['organ', 'comission'], $betw, $objs);
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
        return response()->json(Data::find(new DfdItem(), ['dfd' => $request->id], null, ['item', 'dotation', 'program']));
    }

    /**
     * Lista fornecedores associados a uma coleta de preços.
     *
     * @param Request $request Dados da requisição.
     * @return \Illuminate\Database\Eloquent\Collection|null Resposta JSON com a lista de itens DFD.
     */
    public function list_suppliers(Request $request)
    {
        return response()->json(Data::find(new Supplier(), [
            ['column' => 'name', 'operator' => 'LIKE', 'value' => "%$request->supplier%", 'mode' => 'OR'],
            ['column' => 'cnpj', 'operatot' => 'LIKE', 'value' => "%$request->supplier%", 'mode' => 'OR']
        ], ['name']));
    }

    /**
     * Retorna seleções de dados para uso em formulários ou filtros.
     *
     * @param Request $request Dados da requisição, incluindo chave para especificar a seleção.
     * @return \Illuminate\Http\JsonResponse Resposta JSON com os dados solicitados.
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

            'suppliers' => Data::find(new Supplier(), order:['name']),
            'supplier_modalities' => Supplier::list_modalitys(),
            'supplier_sizes' => Supplier::list_sizes(),

            'proposal_modalities' => Proposal::list_modalitys(),
            'proposal_status' => Proposal::list_status()
        ], Dfd::make_details()), 200);
    }

    private function processProposals(Request $request): void
    {
        if ($this->model->id) {

            $process = json_decode($request->process);
            $suppliers = json_decode($request->suppliers);


            if (!empty($suppliers)) {
                foreach ($suppliers as $supplier) {

                    $token = $this->model->id . $supplier->id . Str::random(16);
                    $hour_send = date('H:i:s');

                    if (
                        !Proposal::firstWhere([
                            ['price_record', $this->model->id],
                            ['supplier', $supplier->id]
                        ])
                    ) {

                        $proposal = new Proposal([
                            'protocol' => $request->protocol,
                            'ip' => $request->ip(),
                            'author' => $request->user()->id,
                            'token' => $token,
                            'date_ini' => $request->date_ini,
                            'hour_ini' => $hour_send,
                            'organ' => Data::getOrgan(),
                            'process' => $process->id,
                            'price_record' => $this->model->id,
                            'supplier' => $supplier->id,
                            'modality' => Proposal::M_MAIL,
                            'status' => Proposal::S_START
                        ]);

                        if ($proposal->save()) {
                            Mail::to($supplier->email)->send(new ProposalRequest(
                                $process,
                                $supplier,
                                $token,
                                $request->protocol,
                                $request->date_ini.' '.$hour_send,
                                $request->date_fin));
                        }
                    }

                }
            }
        }
    }
}
