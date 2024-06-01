<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\User;
use App\Security\Guardian;
use Illuminate\Http\Request;

class Suppliers extends Controller
{
    public function __construct()
    {
        parent::__construct(Supplier::class, User::MOD_CATALOGS);
        Guardian::validateAccess($this->module_id);
    }

    public function list(Request $request)
    {
        return $this->baseList(['name', 'cnpj', 'address'], ['name']);
    }
}
