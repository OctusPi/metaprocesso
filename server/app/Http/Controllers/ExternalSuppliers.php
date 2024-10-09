<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ExternalSuppliers extends Controller
{
  public function __construct()
  {
    parent::__construct(Supplier::class);
  }

  public function save(Request $request)
  {
    return $this->base_save($request, [
      'flag' => Supplier::FLAG_EXTERN,
    ]);
  }

  public function selects(Request $request)
    {
        return response()->json([
            'modalities' => Supplier::list_modalitys(),
            'sizes' => Supplier::list_sizes()
        ]);
    }
}
