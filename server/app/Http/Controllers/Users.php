<?php

namespace App\Http\Controllers;

use App\Http\Middlewares\Data;
use App\Mail\Wellcome;
use App\Models\Organ;
use App\Models\Sector;
use App\Models\Unit;
use App\Models\User;
use App\Utils\Notify;
use App\Utils\Utils;
use Exception;
use Illuminate\Http\Request;
use Log;
use Mail;
use Str;

class Users extends Controller
{
    public function __construct()
    {
        parent::__construct(User::class, User::MOD_USERS['module']);
    }

    public function list(Request $request)
    {
        return $this->base_list(
            $request,
            ['name', 'profile', 'email'],
            ['name']
        );
    }

    public function save(Request $request)
    {
        $validateErros = $this->validate($request->all());

        if ($validateErros) {
            return response()->json(Notify::warning($validateErros), 400);
        }

        if ($request->id) {
            return $this->base_save($request);
        }

        $pass = Str::random(8);
        $user = (new User())->fill($request->all());

        $user->username = $request->email;
        $user->password = $pass;

        if ($user->save()) {
            Mail::to($user)->send(new Wellcome($user, $pass));
            return Response()->json(Notify::success("Usuário cadastrado com sucesso"), 200);
        }

        return Response()->json(Notify::warning("Usuário já cadastrado no sistema"), 400);
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
                'profiles'  => $profiles,
                'organs'    => Utils::map_select(Data::find(Organ::class, order:['name'])),
                'units'     => Utils::map_select(Data::find(Unit::class, order:['name'])),
                'sectors'   => Utils::map_select(Data::find(Sector::class, order:['name'])),
                'status'    => User::list_status(),
                'modules'   => $request->user()->profile != User::PRF_ADMIN
                            ? $request->user()->modules
                            : User::list_modules(),
            ], 200);
        } catch (Exception $th) {
            Log::info($th->getMessage());
            return Response()->json(Notify::error('Falha ao recuperar dados!'), 500);
        }
    }
}
