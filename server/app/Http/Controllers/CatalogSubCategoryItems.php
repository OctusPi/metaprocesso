<?php

namespace App\Http\Controllers;

use App\Middleware\Data;
use App\Models\CatalogSubCategoryItem;
use App\Models\User;
use App\Models\Organ;
use App\Security\Guardian;
use App\Utils\Utils;
use Illuminate\Http\Request;

class CatalogSubCategoryItems extends Controller
{
    public function __construct()
    {
        parent::__construct(CatalogSubCategoryItem::class, User::MOD_CATALOGS);
        Guardian::validateAccess($this->module_id);
    }

    public function list(Request $request)
    {
        return $this->baseList(['name', 'organ'], ['name'], ['organ']);
    }

    public function selects(Request $request)
    {
        return Response()->json([
            'organs' => Utils::map_select(Data::list(Organ::class, order: ['name'])),
        ], 200);
    }
}
