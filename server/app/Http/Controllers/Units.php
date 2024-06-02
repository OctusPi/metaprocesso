<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\User;
use App\Utils\Utils;
use App\Models\Organ;
use App\Middleware\Data;
use App\Security\Guardian;
use Illuminate\Http\Request;

class Units extends Controller
{
    public function __construct()
    {
        parent::__construct(Unit::class, User::MOD_UNITS);
        Guardian::validateAccess($this->module_id);
    }

    public function list(Request $request)
    {
        return $this->baseList(['organ', 'name', 'cnpj'], ['name'], ['organ']);
    }

    public function selects(Request $request)
    {
        return Response()->json([
            'organs' => Utils::map_select(Data::list(Organ::class, order:['name'])),
            'status' => [['id' => 0, 'title' => 'Inativo'], ['id' => 1, 'title' => 'Ativo']]
        ], 200);
    }
}
