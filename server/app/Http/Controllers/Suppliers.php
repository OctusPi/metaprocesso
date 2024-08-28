<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;

class Suppliers extends Controller
{
    public function __construct()
    {
        parent::__construct(Supplier::class, User::MOD_SUPPLIERS['module']);
    }

    public function list(Request $request)
    {
        return $this->base_list($request, ['name', 'cnpj', 'address', 'modality', 'size'], ['name']);
    }

    public function selects(Request $request)
    {
        return response()->json([
            'modalities' => Supplier::list_modalitys(),
            'sizes' => Supplier::list_sizes()
        ]);
    }
}
