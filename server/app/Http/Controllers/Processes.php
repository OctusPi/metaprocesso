<?php

namespace App\Http\Controllers;

use App\Models\Dfd;
use App\Models\Unit;
use App\Utils\Utils;
use App\Models\Organ;
use App\Utils\Notify;
use App\Models\Common;
use App\Models\DfdItem;
use App\Models\Process;
use App\Models\Program;
use App\Models\Unit;
use App\Models\User;
use App\Security\Guardian;
use App\Utils\Notify;
use App\Utils\Utils;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use App\Models\ComissionMember;

class Processes extends Controller
{
    public function __construct()
    {
        parent::__construct(Process::class, User::MOD_PROCCESS);
        Guardian::validateAccess($this->module_id);
    }

    private function setDfdStatus(int $status, Collection $collection)
    {
        $dfd_instance = Dfd::whereIn('id', $collection->pluck('id'))
            ->with('organ', 'unit', 'comission', 'demandant', 'ordinator');

        $matched_status = match ($status) {
            Process::S_ABERTO => Dfd::STATUS_BLOQUEADO,
            Process::S_FINALIZADO => Dfd::STATUS_PROCESSADO,
            default => Dfd::STATUS_ENVIADO
        };

        if ($status != Process::S_FRACASSADO) {
            $dfd_instance->update(['status' => $matched_status]);
        }

        return $dfd_instance->get();
    }

    public function save(Request $request)
    {
        $comission = Comission::find($request->comission);
        if (empty($comission->comissionmembers)) {
            return Response()->json(Notify::warning("A comissão não possui nenhum membro!"), 403);
        }

        $premodel = new Process($request->all());
        $premodel->author = $this->user_loged->id;
        $premodel->ip = $request->ip();
        $premodel->comission_members = $comission->comissionmembers;
        $premodel->comission_address = $comission->unit()->value('address');

        $dfds = $this->setDfdStatus($premodel->status, collect($premodel->dfds));

        $premodel->dfds = $dfds->toArray();
        $premodel->ordinators = $dfds->pluck('ordinator');

        return $this->baseSave(Process::class, $premodel->toArray());
    }

    public function list(Request $request)
    {
        return $this->baseList(
            ['organ', 'comission', 'protocol', 'date_hour_ini', 'type', 'modality', 'status', 'object'],
            ['date_hour_ini'],
            ['organ', 'comission']
        );
    }

    public function list_dfds(Request $request)
    {
        if (empty($request->except('organ', 'units'))) {
            return Response()->json(Notify::warning('Informe pelo menos um campo de busca...'), 500);
        }

        $search = Utils::map_search(['protocol', 'organ', 'description'], $request->all());
        $betw = $request->date_i && $request->date_f ? ['date_ini' => [$request->date_i, $request->date_f]] : null;
        $search_obj = Utils::map_search_obj($request->units, 'units', 'id');

        $query = Data::list(Dfd::class, $search, ['date_ini'], ['organ', 'unit', 'comission', 'demandant', 'ordinator'], $betw, $search_obj);
        return Response()->json($query, 200);
    }

    public function list_dfd_items(Request $request)
    {
        return Data::list(DfdItem::class, ['dfd' => $request->id], null, ['item']);
    }

    public function update(Request $request)
    {
        $instance = Process::find($request->id);
        if (!$instance) {
            return Response()->json(Notify::warning('Registro não localizado!'), 404);
        }

        if ($instance->status >= Process::S_ANULADO) {
            return response()->json(Notify::warning("Não é possível editar o registro!"), 403);
        }

        $process = new Process($request->all());

        $dfds = $this->setDfdStatus($request->status, collect($process->dfds));

        $process->dfds = $dfds->toArray();
        $process->ordinators = $dfds->pluck('ordinator');

        return $this->baseUpdate(Process::class, $request->id, $process->toArray());
    }

    public function delete(Request $request)
    {
        $instance = Process::find($request->id);
        if (!$instance) {
            return Response()->json(Notify::warning('Registro não localizado!'), 404);
        }

        if ($instance->status >= Process::S_ANULADO) {
            return response()->json(Notify::warning("Não é possível excluir o registro!"), 403);
        }

        return parent::delete($request);
    }

    public function selects(Request $request)
    {
        $units = [];
        $comissions = [];
        $ordinators = [];
        $programs = [];
        $dotations = [];

        if ($request->key) {
            $units = Utils::map_select(Data::list(Unit::class, [
                [
                    'column' => $request->key,
                    'operator' => '=',
                    'value' => $request->search,
                    'mode' => 'AND'
                ]
            ], ['name']));

            $comissions = Utils::map_select(Data::list(Comission::class, [
                [
                    'column' => $request->key,
                    'operator' => '=',
                    'value' => $request->search,
                    'mode' => 'AND'
                ]
            ], ['name']));

            $ordinators = Utils::map_select(Data::list(Ordinator::class, [
                [
                    'column' => $request->key,
                    'operator' => '=',
                    'value' => $request->search,
                    'mode' => 'AND'
                ]
            ], ['name']));

            $programs = Utils::map_select(Data::list(Program::class, [
                [
                    'column' => $request->key,
                    'operator' => '=',
                    'value' => $request->search,
                    'mode' => 'AND'
                ]
            ], ['name']));

            $dotations = Utils::map_select(Data::list(Dotation::class, [
                [
                    'column' => $request->key,
                    'operator' => '=',
                    'value' => $request->search,
                    'mode' => 'AND'
                ]
            ], ['name']));
        }

        return Response()->json([
            'organs' => Utils::map_select(Data::list(Organ::class, order: ['name'])),
            'comissions' => $comissions,
            'ordinators' => $ordinators,
            'programs' => $programs,
            'dotations' => $dotations,
            'units' => $units,
            'types' => Process::list_types(),
            'status' => Process::list_status(),
            'dfds_status' => Dfd::list_status(),
            'prioritys_dfd' => Dfd::list_priority(),
            'hirings_dfd' => Dfd::list_hirings(),
            'acquisitions_dfd' => Dfd::list_acquisitions(),
            'modalities' => Process::list_modalitys(),
            'responsibilitys' => ComissionMember::list_responsabilities()
        ], 200);
    }
}
