<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Utils\Notify;
use App\Utils\Utils;
use App\Models\Catalog;
use App\Http\Middlewares\Data;
use App\Models\CatalogItem;
use Illuminate\Http\Request;
use App\Models\CatalogSubCategoryItem;

class CatalogItems extends Controller
{
    public function __construct()
    {
        parent::__construct(CatalogItem::class, User::MOD_CATALOGS['module']);
    }

    public function list(Request $request)
    {
        return $this->base_list(
            $request,
            ['catalog', 'name', 'description', 'status', 'type', 'category', 'subcategory'],
            ['name'],
            ['subcategory'],
            organ: true
        );
    }

    public function save(Request $request)
    {
        $catalog = Catalog::find($request->catalog);

        if ($catalog) {
            return $this->base_save($request, ['catalog' => $catalog->id]);
        }

        return response()->json(Notify::warning("Catálogo não localizado!"), 404);
    }

    public function catalog(Request $request)
    {
        $request->id = $request->catalog;

        return (new Catalogs())->base_details($request, ['organ', 'comission']);
    }

    public function selects(Request $request)
    {
        return Response()->json([
            'types' => CatalogItem::list_types(),
            'categories' => CatalogItem::list_categories(),
            'status' => CatalogItem::list_status(),
            'origins' => CatalogItem::list_origins(),
            'groups' => Utils::map_select(Data::find(CatalogSubCategoryItem::class, order: ['name'])),
        ], 200);
    }
}
