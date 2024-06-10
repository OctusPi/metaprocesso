<?php

namespace App\Http\Controllers;

use App\Models\DfdItem;
use App\Models\User;
use App\Security\Guardian;
use Illuminate\Http\Request;

class DfdsItems extends Controller
{
    public function __construct()
    {
        parent::__construct(DfdItem::class, User::MOD_DFDS);
        Guardian::validateAccess($this->module_id);
    }

    public function list(Request $request)
    {
        return $this->baseList(['name'], ['name'], ['organ', 'unit']);
    }
}
