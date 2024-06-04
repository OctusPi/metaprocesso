<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Utils\Utils;
use App\Models\Organ;
use App\Models\Catalog;
use App\Middleware\Data;
use App\Models\Comission;
use App\Security\Guardian;
use Illuminate\Http\Request;

class Catalogs extends Controller
{
    public function __construct()
    {
        parent::__construct(Catalog::class, User::MOD_CATALOGS);
        Guardian::validateAccess($this->module_id);
    }

    public function list()
    {
        return $this->baseList(['organ', 'comission', 'name'], ['name'], ['organ', 'comission']);
    }

    public function selects(Request $request)
    {
        $comissions = $request->key ? Utils::map_select(Data::list(Comission::class, [
            [
                'column' => $request->key,
                'operator' => '=',
                'value' => $request->search,
                'mode' => 'AND'
            ]
        ], ['name'])) : Utils::map_select(Data::list(Comission::class));

        return Response()->json([
            'organs' => Utils::map_select(Data::list(Organ::class, order: ['name'])),
            'comissions' => $comissions,
        ], 200);
    }
}
