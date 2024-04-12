<?php

namespace App\Http\Controllers;

use App\Mail\ChangePassNotification;
use App\Models\User;
use App\Security\Guardian;
use App\Utils\Dates;
use App\Security\JWT;
use App\Utils\Notify;
use Illuminate\Http\Request;
use App\Mail\ChangePassRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

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
        $user->lastlogin = $user->nowlogin ?? Dates::nowPTBR();
        $user->nowlogin = Dates::nowPTBR();
        $user->save();

        return response()->json([
            'token'      => $token,
            'user'       => ['name'=>$user->name, 'profile' => User::list_profiles()[$user->profile], 'last_login' => $user->lastlogin],
            'navigation' => $user->modules,
            'notify'     => ['type' => Notify::SUCCESS],
            'redirect'   => '/dashboard'
        ], 200);
    }

    public function recover(Request $request)
    {
        $user = User::where("username", $request->username)->first();
        
        if (!$user) {
            return Response()->json(Notify::warning('Usuário não localizado!'), 401);
        }

        try {
            $token = JWT::create($user);
            $user->passchange = true;
            $user->token = $token;

            if($user->save()){
                Mail::to($user)->send(new ChangePassRequest($user, Dates::futurePTBR(1)));
                return Response()->json(Notify::success('E-mail para redefinição de senha enviado!'), 200);
            }

        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return Response()->json(Notify::warning('Falha ao processar dados!'), 500);
        }
        
    }

    public function renew(Request $request){
        $rules = ['newpass' => 'required','confpass'=> 'required'];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response()->json(Notify::warning('Campos Obrigatórios não informados!'), 400);
        }

        if($request->newpass !== $request->confpass){
            return Response()->json(Notify::warning('Senhas divergentes informadas!'), 400);
        }

        if(!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*[^a-zA-Z]).{8,}$/', $request->newpass)){
            return Response()->json(Notify::warning('Senhas não atende aos critérios de segurança!'), 400);
        }

        $token = $request->token;
        $check  = JWT::validate($token);
        $user   = User::where('token', $token)->where('passchange', true)->first();

        if (!$check || !$user) {
            return Response()->json(Notify::warning('Token inválido ou expirado!'), 401);
        }

        $user->password = Hash::make($request->newpass);
        $user->token = null;
        $user->passchange = false;

        if($user->update()){
            Mail::to($user)->send(new ChangePassNotification());
            return Response()->json(Notify::success('Senha alterada com sucesso!'),200);
        }

        return Response()->json(Notify::error('Falha ao cadastrar senha'), 500);
    }

    public function verify(Request $request)
    {
        $token = $request->token;
        $check  = JWT::validate($token);
        $user   = User::where('token', $token)->where('passchange', true)->first();

        if (!$check || !$user) {
            return Response()->json(Notify::warning('Token inválido ou expirado!'), 401);
        }

        return Response()->json(
            [
                'user' => [
                    'name'=>$user->name, 
                    'profile' => $user->profile, 
                    'last_login' => $user->lastlogin]
            ], 200);
    }

    public function check()
    {
       return Guardian::checkToken() 
       ? Response()->json(['token_valid' => true],200) 
       : Response()->json(Notify::info('Login expirado, realize o login novamente...'), 401);
    }
}
