<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Utils\Utils;
use App\Utils\Notify;
use App\Middleware\Data;
use App\Security\Guardian;
use Illuminate\Http\Request;

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
        try {
            $search = Utils::map_search(['name', 'email', 'status'], $request->all());
            $query  = Data::list(User::class, $search);
            return Response()->json($query ?? [], 200);
        } catch (\Throwable $th) {
            return Response()->json(Notify::error('Falha ao recuperar dados!'), 500);
        }
    }

    public function details(Request $request)
    {
        $user = User::where("id", $request->id)->first();
        if (!$user) {
            return Response()->json(Notify::warning('Usuário não localizado!'), 404);
        }
        
        return Response()->json($user->toArray(), 200);
    }

    public function selects(Request $request)
    {
        
        $profiles = [];
        foreach (User::list_profiles() as $key => $value) {
            $profiles[] = ['id' => $key, 'title' => $value];
        }

        if($this->user_loged -> profile != User::PRF_ADMIN){
            array_shift($profiles);
        }


        try {
            return Response()->json([
                'modules'   => $this->user_loged != null ? $this->user_loged->modules : [],
                'organs'    => [],
                'units'     => [],
                'sectors'   => [],
                'profiles'  => $profiles,
                'status'    => [['id'=> 0,'title'=> 'Inativo'], ['id'=> 1,'title'=> 'Ativo']]
            ], 200);
        } catch (\Throwable $th) {
            return Response()->json(Notify::error('Falha ao recuperar dados!'), 500);
        }
    }
}
