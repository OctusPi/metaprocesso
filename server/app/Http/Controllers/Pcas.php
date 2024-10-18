<?php

namespace App\Http\Controllers;

use App\Models\Pca;
use App\Models\User;
use Illuminate\Http\Request;

class Pcas extends Controller
{
    public function __construct()
    {
        parent::__construct(Pca::class, User::MOD_PCA['module']);
    }

    public function selects(Request $request)
    {
        return [
            'status' => Pca::list_status()
        ];
    }
}
