<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Utils\Notify;
use App\Security\Guardian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Management extends Controller
{
    public function __construct()
    {
        parent::__construct(User::MOD_MANAGEMENT);
        Guardian::validateAccess($this->module_id);
    }

    public function save(Request $request)
    {
        
    }
    
    public function update(Request $request)
    {

    }

    public function destroy(Request $request)
    {

    }

    public function list(Request $request)
    {
        
    }

    public function details(Request $request)
    {
        
    }

    public function selects(Request $request)
    {
        try {
            return Response()->json([
                'modules'   => $this->user_loged != null ? $this->user_loged->modules : [],
                'organs'    => [],
                'units'     => [],
                'sectors'   => []
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(Notify::error('Falha ao recuperar dados!'));
        }
    }
}
