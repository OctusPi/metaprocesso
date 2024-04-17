<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\User;
use App\Utils\Utils;
use App\Models\Organ;
use App\Models\Sector;
use App\Middleware\Data;
use App\Security\Guardian;
use Illuminate\Http\Request;

class Sectors extends Controller
{
    public function __construct()
    {
        parent::__construct(User::MOD_MANAGEMENT);
        Guardian::validateAccess($this->module_id);
    }

    public function save(Request $request)
    {
        return $this->baseSave(Sector::class, $request->all());
    }

    public function update(Request $request)
    {
        return $this->baseUpdate(Sector::class, $request->id, $request->all());
    }

    public function delete(Request $request)
    {
        return $this->baseDelete(Sector::class, $request->id, $request->password);
    }

    public function list(Request $request)
    {
        $search = ['organ_id', 'unit_id', 'name'];
        return $this->baseList(Sector::class, $search, $request->all());
    }

    public function details(Request $request)
    {
        return $this->baseDetails(Sector::class, $request->id);
    }

    public function selects(Request $request)
    {
        return Response()->json([
            'organs' => Utils::map_select(Data::list(Organ::class)),
            'units'  => Utils::map_select(Data::list(Unit::class))
        ], 200);
    }
}
