<?php

namespace App\Http\Controllers;

use App\Models\Dfd;
use App\Models\Unit;
use App\Models\User;
use App\Utils\Utils;
use App\Utils\Notify;
use App\Models\DfdItem;
use App\Models\Process;
use App\Models\Program;
use App\Http\Middlewares\Data;
use App\Models\Dotation;
use App\Models\Comission;
use App\Models\Ordinator;
use Illuminate\Http\Request;
use App\Models\ComissionMember;
use Illuminate\Support\Collection;

class Processes extends Controller
{
    public function __construct()
    {
        parent::__construct(Process::class, User::MOD_PROCCESS['module']);
    }

    public function save(Request $request)
    {
        if ($request->has('id')) {
            $instance = Process::find($request->id);

            if (!$instance) {
                return Response()->json(Notify::warning('Registro não localizado!'), 404);
            }

            if ($instance->status >= Process::S_ANULADO) {
                return response()->json(Notify::warning("Não é possível editar o registro!"), 403);
            }

            $process = new Process($request->all());

            $dfds = $this->set_dfd_status($request->status, collect($process->dfds));

            $process->dfds = $dfds->toArray();
            $process->ordinators = $dfds->pluck('ordinator');

            return $this->base_save($request, $process->toArray());
        }

        $comission = Comission::with('organ')->find($request->comission);

        $premodel = new Process($request->all());
        $premodel->author = $request->user()->id;
        $premodel->ip = $request->ip();
        $premodel->comission_members = $comission->comissionmembers;
        $premodel->comission_address = $comission->toArray()['organ']['address'];

        $dfds = $this->set_dfd_status($premodel->status, collect($premodel->dfds));

        $premodel->dfds = $dfds->toArray();
        $premodel->ordinators = $dfds->pluck('ordinator');

        return $this->base_save($request, $premodel->toArray());
    }

    public function list(Request $request)
    {
        return $this->base_list(
            $request,
            ['organ', 'units', 'protocol', 'date_hour_ini', 'status', 'description'],
            ['date_hour_ini'],
            ['organ', 'comission'],
            organ: true
        );
    }

    public function list_dfds(Request $request)
    {
        if (empty($request->except('organ', 'units'))) {
            return Response()->json(Notify::warning('Informe pelo menos um campo de busca...'), 400);
        }

        $search = Utils::map_search(['protocol', 'organ', 'description'], $request->all());
        $betw = $request->date_i && $request->date_f ? ['date_ini' => [$request->date_i, $request->date_f]] : null;

        $query = Data::find(Dfd::class, $search, ['date_ini'], ['organ', 'unit', 'comission', 'demandant', 'ordinator'], $betw);
        return Response()->json($query, 200);
    }

    public function list_dfd_items(Request $request)
    {
        return Data::find(DfdItem::class, ['dfd' => $request->id], null, ['item', 'dotation', 'program']);
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

        return $this->base_delete($request);
    }

    public function selects(Request $request)
    {
        return Response()->json([
            'comissions' => Utils::map_select(Data::find(Comission::class, order: ['name'])),
            'ordinators' => Utils::map_select(Data::find(Ordinator::class, order: ['name'])),
            'programs' => Utils::map_select(Data::find(Program::class, order: ['name'])),
            'dotations' => Utils::map_select(Data::find(Dotation::class, order: ['name'])),
            'units' => Utils::map_select(Data::find(Unit::class, order: ['name'])),
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

    private function set_dfd_status(?int $status, Collection $collection)
    {
        if (empty($status)) {
            return $collection;
        }

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
}
