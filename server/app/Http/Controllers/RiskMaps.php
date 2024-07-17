<?php

namespace App\Http\Controllers;

use App\Middleware\Data;
use App\Models\Comission;
use App\Models\ComissionMember;
use App\Models\Common;
use App\Models\Process;
use App\Models\RiskMap;
use App\Utils\Utils;
use Illuminate\Http\Request;

class RiskMaps extends Controller
{
    public function __construct()
    {
        parent::__construct(RiskMaps::class, true, Common::MOD_RISKINESS['module']);
    }

    public function list(Request $request)
    {
        return $this->baseList(
            ['process', 'comission', 'date_version', 'phase'],
            ['date_version'],
            ['process', 'comission']
        );
    }

    public function save(Request $request)
    {
        return $this->baseSave(RiskMap::class, array_merge($request->all(), [
            'ip' => $request->ip(),
            'user' => $request->user()->id,
            'comission_members' => ComissionMember::where('comission', $request->comission)->get()->toArray(),
        ]));
    }

    public function details(Request $request)
    {
        return $this->baseDetails(RiskMap::class, $request->id, ['process', 'comission']);
    }

    public function selects(Request $request)
    {
        $processes = $request->key ? Utils::map_select(Data::list(Process::class, [
            [
                'column' => $request->key,
                'operator' => '=',
                'value' => $request->search,
                'mode' => 'AND'
            ]
        ], ['name'])) : Utils::map_select(Data::list(Process::class));

        return Response()->json([
            'processes' => $processes,
            'comissions' => Utils::map_select(Data::list(Comission::class, order: ['name'])),
            'phases' => RiskMap::list_phases()
        ], 200);
    }
}
