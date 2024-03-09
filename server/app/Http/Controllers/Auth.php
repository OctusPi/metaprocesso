<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Security\JWT;
use Illuminate\Http\Request;

class Auth extends Controller
{
    public function auth(Request $request)
    {
        $user = User::where("email", $request->email)->first();
        
        if (!$user) {
            response()->json(['alert'=>'warning', 'msg'=>'Usuário não localizado!'], 401);
        }
        
        if(!password_verify($request->password, $user->password)) {
            response()->json(['alert'=>'warning', 'msg'=>'Usuário não localizado!'], 401);
        }

        $token = JWT::create($user);

        return response()->json([
            'token' => $token,
            'user'=> ['name'=>$user->name, 'profile' => $user->profile, 'last_login' => $user->lastlogin]
        ], 200);
    }
}
