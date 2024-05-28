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
        parent::__construct(User::MOD_CATALOGS);
        Guardian::validateAccess($this->module_id);
    }

    public function save(Request $request)
    {
        return $this->baseSave(Catalog::class, $request->all());
    }

    public function update(Request $request)
    {
        return $this->baseUpdate(Catalog::class, $request->id, $request->all());
    }

    public function delete(Request $request)
    {
        return $this->baseDelete(Catalog::class, $request->id, $request->password);
    }

    public function list(Request $request)
    {
        $search = ['organ', 'comission', 'name'];
        return $this->baseList(Catalog::class, $search, $request->all(), ['name'], ['organ', 'comission']);
    }

    public function details(Request $request)
    {
        return $this->baseDetails(Catalog::class, $request->id);
    }

    public function selects(Request $request)
    {
        $comissions = $request->key ? Utils::map_select(Data::list(Comission::class, [
            [
                'column'   => $request->key,
                'operator' => '=',
                'value'    => $request->search,
                'mode'     => 'AND'
            ]
            ], ['name'])) : Utils::map_select(Data::list(Comission::class));

        return Response()->json([
            'organs' => Utils::map_select(Data::list(Organ::class, order:['name'])),
            'comissions'  => $comissions,
            
        ], 200);
    }
}
