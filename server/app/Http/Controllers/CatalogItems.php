<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Utils\Notify;
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
        $catalog = Catalog::where('id', $request->catalog)->first();
        if($catalog){
            $req = array_merge($request->all(), ['catalog' => $catalog->id, 'organ' => $catalog->organ]);
            return $this->baseSave(CatalogItem::class, $req);
        }

        return response()->json(Notify::warning("Registro atualizado com sucesso!"), 404);
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
        $search = ['name', 'description', 'status', 'type', 'category', 'subcategory'];
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
            'origins' => CatalogItem::list_origem()
        ], 200);

        
    }
}
