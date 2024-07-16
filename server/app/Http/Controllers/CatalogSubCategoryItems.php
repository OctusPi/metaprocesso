<?php

namespace App\Http\Controllers;

use App\Models\Organ;
use App\Utils\Notify;
use App\Models\Common;
use Illuminate\Http\Request;
use App\Models\CatalogSubCategoryItem;

class CatalogSubCategoryItems extends Controller
{
    public function __construct()
    {
        parent::__construct(CatalogSubCategoryItem::class, true, Common::MOD_CATALOGS['module']);
    }

    public function save(Request $request)
    {
        $organ = Organ::where('id', $request->organ)->first();
        if ($organ) {
            $req = array_merge($request->all(), ['organ' => $organ->id]);
            return $this->baseSave(CatalogSubCategoryItem::class, $req);
        }

        return response()->json(Notify::warning("Órgão não localizado!"), 404);
    }

    public function list(Request $request)
    {
        return $this->baseList(['name', 'organ'], ['name'], ['organ']);
    }
}
