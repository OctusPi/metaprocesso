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
        parent::__construct(User::MOD_CATALOGS);
        Guardian::validateAccess($this->module_id);
    }

    public function save(Request $request)
    {
        return $this->baseSave(Supplier::class, $request->all());
    }

    public function update(Request $request)
    {
        return $this->baseUpdate(Supplier::class, $request->id, $request->all());
    }

    public function delete(Request $request)
    {
        return $this->baseDelete(Supplier::class, $request->id, $request->password);
    }

    public function list(Request $request)
    {
        $search = ['name', 'cnpj', 'address'];
        return $this->baseList(Supplier::class, $search, $request->all(), ['name']);
    }

    public function details(Request $request)
    {
        return $this->baseDetails(Supplier::class, $request->id);
    }
}
