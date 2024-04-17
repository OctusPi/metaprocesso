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
        parent::__construct(User::MOD_MANAGEMENT);
        Guardian::validateAccess($this->module_id);
    }

    public function save(Request $request)
    {
        return $this->baseSave(Unit::class, $request->all());
    }

    public function update(Request $request)
    {
        return $this->baseUpdate(Unit::class, $request->id, $request->all());
    }

    public function delete(Request $request)
    {
        return $this->baseDelete(Unit::class, $request->id, $request->password);
    }

    public function list(Request $request)
    {
        $search = ['organ_id', 'name', 'cnpj'];
        return $this->baseList(Unit::class, $search, $request->all());
    }

    public function details(Request $request)
    {
        return $this->baseDetails(Unit::class, $request->id);
    }

    public function selects(Request $request)
    {
        return Response()->json([
            'organs' => Utils::map_select(Data::list(Organ::class)),
            'status' => [['id' => 0, 'title' => 'Inativo'], ['id' => 1, 'title' => 'Ativo']]
        ], 200);
    }
}
