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
        return $this->baseSave(Etp::class, $request->all());
    }

    public function update(Request $request)
    {
        return $this->baseUpdate(Etp::class, $request->id, $request->all());
    }

    public function list(Request $request)
    {
        return $this->baseList(['protocol', 'organ', 'emission', 'comission', 'necessity'], ['protocol']);
    }

    public function details(Request $request)
    {
        return $this->baseDetails(Etp::class, $request->id);
    }

    public function selects(Request $request)
    {
        $comission = $request->key && $request->key == 'organ' ? Utils::map_select(Data::list(Comission::class, [
            [
                'column' => $request->key,
                'operator' => '=',
                'value' => $request->search,
                'mode' => 'AND'
            ]
        ], ['name'])) : Utils::map_select(Data::list(Comission::class));

        return Response()->json([
            'dfds' => Utils::map_select(Data::list(Dfd::class, order: ['code'])),
            'organs' => Utils::map_select(Data::list(Organ::class, order: ['name'])),
            'status' => Etp::list_status(),
            'comissions' => $comission,
        ], 200);
    }
}
