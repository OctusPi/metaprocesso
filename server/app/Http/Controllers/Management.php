<?php

namespace App\Http\Controllers;

use App\Models\Sector;
use App\Models\Unit;
use App\Models\User;
use App\Utils\Utils;
use App\Models\Organ;
use App\Utils\Notify;
use App\Mail\Wellcome;
use App\Middleware\Data;
use App\Security\Guardian;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class Management extends Controller
{
    public function __construct()
    {
        parent::__construct(User::MOD_MANAGEMENT);
        Guardian::validateAccess($this->module_id);
    }

    public function save(Request $request)
    {
        $validateErros = $this->validateErros(User::class, $request->all());
        if ($validateErros) {
            return response()->json(Notify::warning($validateErros), 400);
        }

        $pass = Str::random(8);
        $user = new User($request->all());
        $user->setAttribute('username',$request->input("email"));
        $user->setAttribute('password',Hash::make($pass));

        if($user->save()){
            Mail::to($user)->send(new Wellcome($user, $pass));
            return Response()->json(Notify::success("Usuário cadastrado com sucesso"), 200);
        }

        return Response()->json(Notify::warning("Usuário já cadastrado no sistema"), 400);
    }
    
    public function update(Request $request)
    {
        return $this->baseUpdate(User::class, $request->id, $request->all());
    }

    public function delete(Request $request)
    {
        return $this->baseDelete(User::class, $request->id, $request->password);
    }

    public function list(Request $request)
    {
        $search = ['name', 'email', 'profile'];
        return $this->baseList(User::class, $search, $request->all());
    }

    public function details(Request $request)
    {
        return $this->baseDetails(User::class, $request->id);
    }

    public function selects(Request $request)
    {
        
        $profiles = [];
        foreach (User::list_profiles() as $key => $value) {
            if($key >= $this->user_loged->profile){
                $profiles[] = ['id' => $key, 'title' => $value];
            }
        }

        try {
            return Response()->json([
                'modules'   => $this->user_loged->profile != User::PRF_ADMIN 
                            ? $this->user_loged->modules 
                            : User::list_modules(),

                'organs'    => Utils::map_select(Data::list(Organ::class, order:['name'])),
                'units'     => Utils::map_select(Data::list(Unit::class, order:['name'])),
                'sectors'   => Utils::map_select(Data::list(Sector::class, order:['name'])),
                'profiles'  => $profiles,
                'status'    => [['id'=> 0,'title'=> 'Inativo'], ['id'=> 1,'title'=> 'Ativo']]
            ], 200);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return Response()->json(Notify::error('Falha ao recuperar dados!'), 500);
        }
    }
}
