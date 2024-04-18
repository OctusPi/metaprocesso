<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\Unit;
use App\Models\User;
use App\Utils\Utils;
use App\Models\Organ;
use App\Middleware\Data;
use App\Security\Guardian;
use Illuminate\Http\Request;

class Programs extends Controller
{
    public function __construct()
    {
        parent::__construct(User::MOD_MANAGEMENT);
        Guardian::validateAccess($this->module_id);
    }

    public function save(Request $request)
    {
        return $this->baseSave(Program::class, $request->all());
    }

    public function update(Request $request)
    {
        return $this->baseUpdate(Program::class, $request->id, $request->all());
    }

    public function delete(Request $request)
    {
        return $this->baseDelete(Program::class, $request->id, $request->password);
    }

    public function list(Request $request)
    {
        $search = ['organ_id', 'unit_id', 'name'];
        return $this->baseList(Program::class, $search, $request->all());
    }

    public function details(Request $request)
    {
        return $this->baseDetails(Program::class, $request->id);
    }

    public function selects(Request $request)
    {
        $units = $request->key ? Utils::map_select(Data::list(Unit::class, [
            [
                'column'   => $request->key,
                'operator' => '=',
                'value'    => $request->search,
                'mode'     => 'AND'
            ]
            ], ['name'])) : Utils::map_select(Data::list(Unit::class));

        return Response()->json([
            'organs' => Utils::map_select(Data::list(Organ::class, order:['name'])),
            'units'  => $units,
            'status' => [['id'=>0, 'title'=>'Inativo'], ['id'=>1, 'title'=>'Ativo']],
        ], 200);
    }
}
