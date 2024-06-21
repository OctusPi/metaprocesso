<?php

namespace App\Http\Controllers;

use App\Models\CatalogSubCategoryItem;
use App\Models\User;
use App\Models\Organ;
use App\Security\Guardian;
use App\Utils\Notify;
use Illuminate\Http\Request;

class CatalogSubCategoryItems extends Controller
{
    public function __construct()
    {
        parent::__construct(CatalogSubCategoryItem::class, User::MOD_CATALOGS);
        Guardian::validateAccess($this->module_id);
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
