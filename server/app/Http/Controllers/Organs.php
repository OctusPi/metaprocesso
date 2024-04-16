<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Organ;
use App\Security\Guardian;
use Illuminate\Http\Request;

class Organs extends Controller
{
    public function __construct()
    {
        parent::__construct(User::MOD_MANAGEMENT);
        Guardian::validateAccess($this->module_id);
    }

    public function save(Request $request)
    {
        return $this->baseSave(Organ::class, $request->all());
    }

    public function update(Request $request)
    {
        return $this->baseUpdate(Organ::class, $request->id, $request->all());
    }

    public function delete(Request $request)
    {
        return $this->baseDelete(Organ::class, $request->id, $request->password);
    }

    public function list(Request $request)
    {
        $search = ['name', 'cnpj', 'postalcity'];
        return $this->baseList(Organ::class, $search, $request->all());
    }

    public function details(Request $request)
    {
        return $this->baseDetails(Organ::class, $request->id);
    }

    public function selects(Request $request)
    {
        return Response()->json([
            'status' => [['id' => 0, 'title' => 'Inativo'], ['id' => 1, 'title' => 'Ativo']]
        ], 200);
    }
}
