<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Utils\Utils;
use App\Utils\Notify;
use App\Models\Catalog;
use App\Http\Middlewares\Data;
use App\Models\Comission;
use App\Models\CatalogItem;
use Illuminate\Http\Request;

class Catalogs extends Controller
{
    public function __construct()
    {
        parent::__construct(Catalog::class, User::MOD_CATALOGS['module']);
    }

    public function list(Request $request)
    {
        return $this->base_list(
            $request,
            ['comission', 'name', 'description'],
            ['name'],
            ['organ', 'comission'],
            organ: true
        );
    }

    public function export(Request $request)
    {
        $instance = Catalog::with(['organ', 'comission'])
            ->find($request->id)
            ->toArray();

        $instance['items'] = CatalogItem::where('catalog', $request->id)->get();

        if (!$instance) {
            return Response()->json(Notify::warning('Catálogo não localizado...'), 404);
        }

        return Response()->json($instance, 200);
    }

    public function selects(Request $request)
    {
        $comissions = $request->key ? Utils::map_select(Data::find(Comission::class, [
            [
                'column' => $request->key,
                'operator' => '=',
                'value' => $request->search,
                'mode' => 'AND'
            ]
        ], ['name'])) : Utils::map_select(Data::find(Comission::class));

        return Response()->json([
            'comissions' => $comissions,
            'items_status' => CatalogItem::list_status(),
            'items_categories' => CatalogItem::list_categories(),
            'items_origins' => CatalogItem::list_origins(),
        ], 200);
    }
}
