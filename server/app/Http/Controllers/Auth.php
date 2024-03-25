<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Utils\Dates;
use App\Security\JWT;
use App\Utils\Notify;
use Illuminate\Http\Request;
use App\Mail\ChangePassRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class Auth extends Controller
{
    public function auth(Request $request)
    {
        $user = User::where("username", $request->username)->first();
        
        if (!$user) {
            return response()->json(Notify::warning('Acesso não autorizado!'), 401);
        }
        
        if(!password_verify($request->password, $user->password)) {
            return response()->json(Notify::warning('Acesso não autorizado!'), 401);
        }

        if($user->status !== 1){
            return response()->json(Notify::warning('Acesso bloqueado pelo administrador!'), 401);
        }

        $token = JWT::create($user);
        $user->lastlogin = $user->nowlogin ?? Dates::nowUTC();
        $user->nowlogin = Dates::nowPTBR();
        $user->modules = json_encode(User::list_modules());
        $user->save();


        return response()->json([
            'token'      => $token,
            'user'       => ['name'=>$user->name, 'profile' => $user->profile, 'last_login' => $user->lastlogin],
            'navigation' => $user->modules,
            'notify'     => ['type' => Notify::SUCCESS],
            'redirect'   => '/home'
        ], 200);
    }

    public function verify(Request $request)
    {
        $authorization = $request->server('HTTP_AUTHORIZATION');
        return JWT::validate($authorization)
        ? Response()->json(['token_valid' => true], 200)
        : Response()->json(Notify::warning('Sessão Expirada, realize o login novamente'), 403);
    }

    public function recover(Request $request)
    {
        $user = User::where("username", $request->username)->first();
        
        if (!$user) {
            return Response()->json(Notify::warning('Usuário não localizado!'), 401);
        }

        try {
            $token = JWT::create($user->password);
            $user->passchange = true;
            $user->token = $token;

            if($user->save()){
                Mail::to($user)->send(new ChangePassRequest($user, Dates::futurePTBR(1)));
                return Response()->json(Notify::success('E-mail para redefinição de senha enviado!'), 200);
            }

        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            Log::error($th->getTraceAsString());
            return Response()->json(Notify::warning('Falha ao processar dados!'), 500);
        }
        
    }
}
