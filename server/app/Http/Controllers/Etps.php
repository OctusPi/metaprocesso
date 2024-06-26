<?php

namespace App\Http\Controllers;

use App\Models\Comission;
use App\Models\Dfd;
use App\Models\Organ;
use App\Models\User;
use App\Utils\Utils;
use App\Middleware\Data;
use App\Models\Etp;
use App\Security\Guardian;
use Illuminate\Http\Request;

class Etps extends Controller
{
    public function __construct()
    {
        parent::__construct(Etp::class, User::MOD_ETPS);
        Guardian::validateAccess($this->module_id);
    }

    public function save(Request $request)
    {
        $user = Guardian::getUser();
        \Log::info($user);
        $autoFilledData = ['ip' => $request->ip(),'user' => $user->id];

        return $this->baseSave(Etp::class, array_merge($request->all(), $autoFilledData));
    }

    public function update(Request $request)
    {
        return $this->baseUpdate(Etp::class, $request->id, $request->all());
    }

    public function list(Request $request)
    {
        return $this->baseList(['protocol', 'organ', 'emission', 'comission', 'necessity', 'status'], ['protocol'], ['organ', 'comission']);
    }

    public function details(Request $request)
    {
        return $this->baseDetails(Etp::class, $request->id);
    }

    public function selects(Request $request)
    {
        $comissions = $request->key && $request->key == 'organ' ? Utils::map_select(Data::list(Comission::class, [
            [
                'column' => $request->key,
                'operator' => '=',
                'value' => $request->search,
                'mode' => 'AND'
            ]
        ], ['name'])) : Utils::map_select(Data::list(Comission::class));

        $dfds = $request->key && $request->key == 'organ' ? Utils::map_select(Data::list(Dfd::class, [
            [
                'column' => $request->key,
                'operator' => '=',
                'value' => $request->search,
                'mode' => 'AND'
            ]
        ], ['protocol'])) : Utils::map_select(Data::list(Dfd::class));

        return Response()->json([
            'organs' => Utils::map_select(Data::list(Organ::class, order: ['name'])),
            'dfds' => $dfds,
            'status' => Etp::list_status(),
            'comissions' => $comissions,
        ], 200);
    }
}
