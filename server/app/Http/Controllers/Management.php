<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\User;
use App\Utils\Utils;
use App\Models\Organ;
use App\Utils\Notify;
use App\Mail\Wellcome;
use App\Models\Common;
use App\Models\Sector;
use App\Middleware\Data;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class Management extends Controller
{
    public function __construct()
    {
        parent::__construct(User::class, true, Common::MOD_USERS['module']);
    }

    public function save(Request $request)
    {
        $validateErros = $this->validateErros(User::class, $request->all());
        if ($validateErros) {
            return response()->json(Notify::warning($validateErros), 400);
        }

        if($request->id){
            return $this->baseSave($request->all());
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

    public function list(Request $request)
    {
        return $this->baseList(['name', 'email', 'profile'], ['name']);
    }

    public function selects(Request $request)
    {
        
        $profiles = [];
        foreach (User::list_profiles() as $key => $value) {
            if($key >= $request->user()->profile){
                $profiles[] = ['id' => $key, 'title' => $value];
            }
        }

        try {
            return Response()->json([
                'modules'   => $request->user()->profile != Common::PRF_ADMIN
                            ? $request->user()->modules 
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
