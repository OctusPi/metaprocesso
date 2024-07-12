<?php

namespace App\Http\Controllers;

use App\Models\CatalogItem;
use App\Models\User;
use App\Utils\Notify;
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

    public function export(Request $request)
    {
        $instance = Catalog::where('id', $request->id)->with([
            'organ',
            'comission'
        ])->first()->toArray();

        $instance['items'] = CatalogItem::where('catalog', $request->id)->get();

        if (!$instance) {
            return Response()->json(Notify::warning('instance não localizado...'), 404);
        }

        return Response()->json($instance, 200);
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
            'items_status' => CatalogItem::list_status(),
            'items_categories' => CatalogItem::list_categories(),
            'items_origins' => CatalogItem::list_origins(),
        ], 200);
    }
}
