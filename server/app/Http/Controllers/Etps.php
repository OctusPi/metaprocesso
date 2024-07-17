<?php

namespace App\Http\Controllers;

use App\Models\Dfd;
use App\Models\Etp;
use App\Models\Unit;
use App\Utils\Utils;
use App\Models\Organ;
use App\Utils\Notify;
use App\Models\Common;
use App\Models\DfdItem;
use App\Models\Process;
use App\Models\Program;
use App\Middleware\Data;
use App\Models\Dotation;
use App\Models\Comission;
use App\Models\PriceRecord;
use Illuminate\Http\Request;
use App\Models\ComissionMember;

class Etps extends Controller
{
    public function __construct()
    {
        parent::__construct(Etp::class, true, Common::MOD_ETPS['module']);
    }

    public function save(Request $request)
    {
        return $this->baseSave(array_merge($request->all(), [
            'ip' => $request->ip(),
            'user' => $request->user()->id,
        ]));
    }

    public function list(Request $request)
    {
        return $this->baseList(
            ['protocol', 'organ', 'emission', 'comission', 'necessity', 'status'],
            ['protocol'],
            ['organ', 'comission', 'process']
        );
    }

    public function details(Request $request)
    {
        return $this->baseDetails($request->id, ['process']);
    }

    public function list_processes(Request $request)
    {

        if (empty($request->except('organ', 'comission'))) {
            return Response()->json(Notify::warning('Informe pelo menos um campo de busca...'), 500);
        }

        $search = Utils::map_search(['protocol', 'organ', 'comission', 'description'], $request->all());
        $betw = $request->date_i && $request->date_f ? ['date_hour_ini' => [$request->date_i, $request->date_f]] : null;

        $query = Data::list(Process::class, $search, null, ['organ', 'comission'], $betw);
        return Response()->json($query, 200);
    }

    public function list_dfd_items(Request $request)
    {
        return Data::list(DfdItem::class, ['dfd' => $request->id], null, ['item']);
    }

    public function selects(Request $request)
    {
        if ($request->key == 'comission') {
            return Response()->json(Data::list(
                ComissionMember::class,
                ['comission' => $request->search],
                ['responsibility']
            )
            );
        }

        $units = [];
        $comissions = [];
        $programs = [];
        $dotations = [];

        if ($request->key) {
            $units = $request->key == 'organ' 
            ? Utils::map_select(Data::list(Unit::class, [$request->key => $request->search], ['name'])) 
            : Utils::map_select(Data::list(Unit::class));

            $comissions = Utils::map_select(Data::list(Comission::class, [$request->key => $request->search], ['name']));

            $programs = Utils::map_select(Data::list(Program::class, [$request->key => $request->search], ['name']));

            $dotations = Utils::map_select(Data::list(Dotation::class, [$request->key => $request->search], ['name']));
        }

        return Response()->json([
            'organs' => Utils::map_select(Data::list(Organ::class, order: ['name'])),
            'units' => $units,
            'comissions' => $comissions,
            'prioritys_dfd' => Dfd::list_priority(),
            'hirings_dfd' => Dfd::list_hirings(),
            'acquisitions_dfd' => Dfd::list_acquisitions(),
            'status' => PriceRecord::list_status(),
            'status_process' => Process::list_status(),
            'status_dfds' => Dfd::list_status(),
            'programs' => $programs,
            'dotations' => $dotations,
            'responsibilitys' => ComissionMember::list_responsabilities()
        ], 200);
    }
}
