<?php

namespace App\Http\Controllers;

use App\Models\CatalogSubCategoryItem;
use App\Models\User;
use Illuminate\Http\Request;

class CatalogSubCategories extends Controller
{
    public function __construct()
    {
        parent::__construct(CatalogSubCategoryItem::class, User::MOD_CATALOGS['module']);
    }

    public function list(Request $request)
    {
        return $this->base_list(
            $request,
            ['name'],
            ['name']
        );
    }
}
