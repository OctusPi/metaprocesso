<?php

namespace App\Http\Controllers;

use App\Models\Organ;
use App\Models\User;
use Illuminate\Http\Request;

class Dashboard extends Controller
{
    public function __construct()
    {
        parent::__construct(Organ::class, User::MOD_MANAGEMENT['module']);
    }

    public function fill(Request $request)
    {
      return response()->json([]);
    }
}
