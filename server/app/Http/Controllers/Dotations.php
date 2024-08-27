<?php

namespace App\Http\Controllers;

use App\Http\Middlewares\Data;
use App\Models\Dotation;
use App\Models\Program;
use App\Models\Unit;
use App\Models\User;
use App\Utils\Utils;
use Illuminate\Http\Request;

class Dotations extends Controller
{
    public function __construct()
    {
        parent::__construct(Dotation::class, User::MOD_DOTATIONS['module']);
    }

    public function list(Request $request)
    {
        return $this->base_list(
            $request,
            ['unit', 'name', 'description'],
            ['name'],
            ['organ', 'unit'],
            organ: true
        );
    }

    public function selects(Request $request)
    {
        return Response()->json([
            'units' => Utils::map_select(Data::find(Unit::class, order: ['name'])),
            'status' => Program::list_status(),
        ], 200);
    }
}
