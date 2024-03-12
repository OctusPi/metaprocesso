<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Security\JWT;
use App\Utils\Notify;
use Illuminate\Http\Request;

class Auth extends Controller
{
    public function auth(Request $request)
    {
        $user = User::where("username", $request->username)->first();
        
        if (!$user) {
            return response()->json(Notify::WARNING('Acesso nãsso autorizado!'), 401);
        }
        
        if(!password_verify($request->password, $user->password)) {
            return response()->json(Notify::WARNING('Acesso não autorizado!'), 401);
        }

        $token = JWT::create($user);

        return response()->json([
            'notify' => ['type' => Notify::SUCCESS],
            'token'  => $token,
            'user'   => ['name'=>$user->name, 'profile' => $user->profile, 'last_login' => $user->lastlogin]
        ], 200);
    }
}
