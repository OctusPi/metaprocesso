<?php

namespace App\Http\Controllers;

use App\Models\Common;
use App\Models\DfdItem;
use Illuminate\Http\Request;

class DfdsItems extends Controller
{
    public function __construct()
    {
        parent::__construct(DfdItem::class, true, Common::MOD_DFDS['module']);
    }

    public function list(Request $request)
    {
        return $this->baseList(['name'], ['name'], ['organ', 'unit']);
    }
}
