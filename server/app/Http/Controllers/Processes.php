<?php

namespace App\Http\Controllers;

use App\Models\Dfd;
use App\Models\Unit;
use App\Models\User;
use App\Utils\Utils;
use App\Utils\Notify;
use App\Models\DfdItem;
use App\Models\Process;
use App\Models\Comission;
use Illuminate\Http\Request;
use App\Http\Middlewares\Data;
use App\Models\ComissionMember;
use Illuminate\Support\Collection;

class Processes extends Controller
{
    /**
     * Constrói o controller Processes.
     */
    public function __construct()
    {
        parent::__construct(Process::class, User::MOD_PROCESSES['module']);
    }

    /**
     * Salva ou atualiza um processo.
     *
     * @param Request $request Dados da requisição para salvar um processo.
     * @return \Illuminate\Http\JsonResponse Resposta JSON do resultado da operação.
     */
    public function save(Request $request)
    {
        if ($request->id) {
            $instance = Data::findOne($this->model, ['id' => $request->id]);

            if (!$instance) {
                return response()->json(Notify::warning('Processo não localizado!'), 404);
            }

            if ($instance->status >= Process::S_ANULADO) {
                return response()->json(Notify::warning("Não é possível editar o registro!"), 403);
            }
        }

        $comission = Data::findOne(new Comission(), ['id' => $request->comission_id], with: ['organ']);
        $comissionmembers = Data::find(new ComissionMember(), ['comission_id' => $request->comission_id]);

        if ($comissionmembers->isEmpty()) {
            return response()->json(Notify::warning('Comissão selecionada não possuí membros ativos'), 400);
        }

        $this->model->fill($request->all());

        $this->model->dfds = json_decode($request->dfds);
        $this->model->author_id = $request->user()->id;
        $this->model->ip = $request->ip();

        $this->model->comission_members = $comissionmembers->toArray();
        $this->model->comission_address = $comission->organ->address;

        $dfds = $this->update_dfds(
            $this->model->status,
            collect($this->model->dfds)
        );


        $this->model->dfds = $dfds->toArray();
        $this->model->ordinators = collect($dfds->toArray())
            ->pluck('ordinator')
            ->toArray();

        return $this->base_save($request, $this->model->toArray());
    }

    /**
     * Lista os processos com base em critérios específicos de filtragem.
     *
     * @param Request $request Dados da requisição incluindo filtros.
     * @return \Illuminate\Http\JsonResponse Resposta JSON com a lista de processos.
     */
    public function list(Request $request)
    {
        $date_between = [
            'date_hour_ini' => [
                $request->date_ini ?: (date('Y')-1) . '-01-01',
                $request->date_fin ?: (date('Y')+1) . '-12-31'
            ]
        ];

        $objs = Utils::map_search_obj($request->units, 'units', 'id');

        return $this->base_list(
            $request,
            ['protocol', 'date_hour_ini', 'status', 'description'],
            ['date_hour_ini', 'desc'],
            ['organ', 'comission'],
            $date_between,
            $objs
        );
    }

    /**
     * Lista os DFDs com base em critérios de busca.
     *
     * @param Request $request Dados da requisição incluindo filtros.
     * @return \Illuminate\Http\JsonResponse Resposta JSON com a lista de DFDs.
     */
    public function list_dfds(Request $request)
    {
        if (!$request->units) {
            return response()->json(Notify::warning('Informe ao menos uma unidade antes de continuar'), 400);
        }

        if (!$request->anyFilled('date_i', 'date_f', 'protocol', 'description', 'units')) {
            return response()->json(Notify::warning('Informe pelo menos um campo de busca...'), 400);
        }

        $search = Utils::map_search(['protocol', 'description'],$request->all());

        $between = [];
        if ($request->date_i && $request->date_f) {
            $between['date_ini'] = [$request->date_i, $request->date_f];
        }

        $query = Data::query(new Dfd(), $search, ['date_ini'], ['unit', 'comission', 'demandant', 'ordinator'], $between);
        
        if($query != null) {
            $units = json_decode($request->units) ?? [];

            $query->where(function ($query) use ($units) {
                foreach ($units as $unit) {
                    $query->orWhere('unit_id', '=', $unit);
                }
            });
        }

        return response()->json($query->get());
    }

    /**
     * Lista os itens de um DFD específico.
     *
     * @param Request $request Dados da requisição incluindo o ID do DFD.
     * @return \Illuminate\Http\JsonResponse Resposta JSON com os itens do DFD.
     */
    public function list_dfd_items(Request $request)
    {
        return response()->json(
            Data::find(new DfdItem(), ['dfd_id' => $request->id], null, ['item', 'dotation', 'program']),
            200
        );
    }

    /**
     * Exclui um processo específico.
     *
     * @param Request $request Dados da requisição incluindo o ID do processo.
     * @return \Illuminate\Http\JsonResponse Resposta JSON do resultado da operação.
     */
    public function delete(Request $request)
    {
        $instance = Data::findOne(new Process(), ['id' => $request->id]);

        if (!$instance) {
            return response()->json(Notify::warning('Registro não localizado!'), 404);
        }

        if ($instance->status >= Process::S_ANULADO) {
            return response()->json(Notify::warning("Não é possível excluir o registro!"), 403);
        }

        return $this->base_delete($request);
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
            'comissions' => Utils::map_select(Data::find(new Comission(), ['status' => Comission::STATUS_ACTIVE], ['name'])),
            'units' => Utils::map_select(Data::find(new Unit(), [], ['name'])),
            'types' => Process::list_types(),
            'status' => Process::list_status(),
            'acquisition_types' => Process::list_acquisition_types(),
            'acquisitions' => Process::list_acquisitions(),
            'installment_types' => Process::list_installments_types(),
            'vars' => [
                'INSTALLMENT_ITEM' => Process::INSTALLMENT_ITEM,
                'ORIGIN_PROCESS' => User::MOD_PROCESSES['id'],
            ]
        ], Dfd::make_details()), 200);
    }

    /**
     * Atualiza o status dos DFDs associados a um processo.
     *
     * @param int|null $status Status do processo.
     * @param Collection $collection Coleção de DFDs.
     * @return Collection Coleção de DFDs atualizada.
     */
    private function update_dfds(?int $status, Collection $collection)
    {
        if ($status != null) {
            return $collection;
        }

        $dfds = Dfd::whereIn('id', $collection->pluck('id'))
            ->with('unit', 'comission', 'demandant', 'ordinator');

        if ($status != Process::S_FRACASSADO) {
            $dfds->update([
                'status' => match ($status) {
                    Process::S_ABERTO, Process::S_BUILD => Dfd::STATUS_BLOQUEADO,
                    Process::S_FINALIZADO => Dfd::STATUS_PROCESSADO,
                    default => Dfd::STATUS_ENVIADO
                }
            ]);

        }

        return $dfds->get();
    }
}
