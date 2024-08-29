<?php

namespace App\Http\Controllers;

use App\Models\CatalogItem;
use App\Models\Dfd;
use App\Models\Etp;
use App\Models\User;
use App\Utils\Utils;
use App\Models\Organ;
use App\Utils\Notify;
use App\Models\DfdItem;
use App\Models\Process;
use App\Models\Comission;
use App\Models\PriceRecord;
use Illuminate\Http\Request;
use App\Models\ComissionMember;
use App\Http\Middlewares\Data;

class Etps extends Controller
{
    public function __construct()
    {
        parent::__construct(Etp::class, User::MOD_ETPS['module']);
    }

    public function save(Request $request)
    {
        return $this->base_save($request, [
            'ip' => $request->ip(),
            'user' => $request->user()->id,
        ]);
    }

    public function list(Request $request)
    {
        return $this->base_list(
            $request,
            ['protocol', 'organ', 'emission', 'comission', 'necessity', 'status'],
            ['protocol'],
            ['organ', 'comission', 'process'],
            organ: true
        );
    }

    public function details(Request $request)
    {
        return $this->base_details($request, ['process', 'process.organ']);
    }

    public function list_processes(Request $request)
    {

        if (empty($request->except('organ', 'comission'))) {
            return Response()->json(Notify::warning('Informe pelo menos um campo de busca...'), 500);
        }

        $search = Utils::map_search(['protocol', 'organ', 'comission', 'description'], $request->all());
        $betw = $request->date_i && $request->date_f ? ['date_hour_ini' => [$request->date_i, $request->date_f]] : null;

        $query = Data::find(Process::class, $search, null, ['organ', 'comission'], $betw);
        return Response()->json($query, 200);
    }

    public function list_dfd_items(Request $request)
    {
        return Data::find(DfdItem::class, ['dfd' => $request->id], null, ['item', 'dotation', 'program']);
    }

    public function selects(Request $request)
    {
        if ($request->key == 'comission') {
            return Response()->json(
                Data::find(
                    ComissionMember::class,
                    ['comission' => $request->search],
                    ['responsibility']
                )
            );
        }

        return Response()->json([
            'organs' => Utils::map_select(Data::find(Organ::class, order: ['name'])),
            'comissions' => Utils::map_select(Data::find(Comission::class, order: ['name'])),
            'prioritys_dfd' => Dfd::list_priority(),
            'hirings_dfd' => Dfd::list_hirings(),
            'acquisitions_dfd' => Dfd::list_acquisitions(),
            'items_types' => CatalogItem::list_types(),
            'status' => PriceRecord::list_status(),
            'status_process' => Process::list_status(),
            'status_dfds' => Dfd::list_status(),
            'responsibilitys' => ComissionMember::list_responsabilities()
        ], 200);
    }
}
