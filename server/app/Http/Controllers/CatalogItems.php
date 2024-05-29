<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Catalog;
use App\Security\Guardian;
use App\Models\CatalogItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CatalogItems extends Controller
{
    public function __construct()
    {
        parent::__construct(User::MOD_CATALOGS);
        Guardian::validateAccess($this->module_id);
    }

    public function save(Request $request)
    {
        return $this->baseSave(CatalogItem::class, $request->all());
    }

    public function update(Request $request)
    {
        return $this->baseUpdate(CatalogItem::class, $request->id, $request->all());
    }

    public function delete(Request $request)
    {
        return $this->baseDelete(CatalogItem::class, $request->id, $request->password);
    }

    public function list(Request $request)
    {
        $search = ['code', 'type', 'category', 'subcategory', 'name', 'description'];
        return $this->baseList(CatalogItem::class, $search, $request->all());
    }

    public function details(Request $request)
    {
        return $this->baseDetails(CatalogItem::class, $request->id);
    }

    public function catalog(Request $request)
    {
        return $this->baseDetails(Catalog::class, $request->catalog, ['organ', 'comission']);
    }

    public function selects()
    {
        return Response()->json([
            'types' => CatalogItem::list_tipo(),
            'categories' => CatalogItem::list_categoria(),
            'groups' => [],
            'status' => CatalogItem::list_status(),
            'unds' => CatalogItem::list_unds()
        ], 200);

        
    }
}
