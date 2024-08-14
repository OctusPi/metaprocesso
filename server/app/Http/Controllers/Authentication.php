<?php

namespace App\Http\Controllers;

use App\Mail\ChangePassNotification;
use App\Models\Organ;
use App\Models\User;
use App\Security\Guardian;
use App\Utils\Dates;
use App\Utils\Notify;
use Illuminate\Http\Request;
use App\Mail\ChangePassRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class Authentication extends Controller
{
    /**
     * Execute authentication user system
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {

        $user = User::where("username", $request->username)->first();

        if (!$user) {
            return response()->json(Notify::warning('Acesso não autorizado!'), 401);
        }

        if ($user->status !== 1) {
            return response()->json(Notify::warning('Acesso bloqueado pelo administrador!'), 401);
        }

        if (Auth::attempt($request->only('username', 'password'))) {
            $user = Auth::user();
            $user->update(['lastlogin' => $user->nowlogin, 'nowlogin' => Dates::nowPTBR()]);
            $abilities = $user->profile == User::PRF_ADMIN ? ['*'] : array_column($user->modules, 'module');
            $token = $user->createToken('authToken', $abilities, now()->addHours(3));

            return response()->json([
                'notify' => ['type' => Notify::SUCCESS],
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'profile' => User::list_profiles()[$user->profile],
                    'organs' => $user->profile != User::PRF_ADMIN ? $user->organs : Organ::get(),
                    'last_login' => $user->getAttribute('lastlogin'),
                    'navigation' => $user->modules,
                    'token' => $token->plainTextToken,
                ]
            ], 200);
        }

        return response()->json(Notify::warning('Usuário ou Senha Inválidos!'), 403);
    }

    /**
     * Request recover password by email
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function recover(Request $request)
    {
        $user = User::where("username", $request->username)->first();

        if (!$user) {
            return response()->json(Notify::warning('Usuário não localizado!'), 401);
        }

        try {
            $token = $user->createToken('recoverpass', ['pass-recover'], now()->addHour());
            $user->token = $token;
            $user->passchange = true;

            if ($user->save()) {
                Mail::to($user)->send(new ChangePassRequest($user, Dates::futurePTBR(1)));
                return response()->json(Notify::success('E-mail para redefinição de senha enviado!'), 200);
            }

            return response()->json(Notify::error('Falha ao solicitar recuperação de senha, contate o administrador!'), 500);

        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json(Notify::warning('Falha ao processar dados!'), 500);
        }

    }

    /**
     * Register new password by token seding in email
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function renew(Request $request)
    {
        $rules = ['newpass' => 'required', 'confpass' => 'required'];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(Notify::warning('Campos Obrigatórios não informados!'), 400);
        }

        if ($request->newpass !== $request->confpass) {
            return response()->json(Notify::warning('Senhas divergentes informadas!'), 400);
        }

        if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*[^a-zA-Z]).{8,}$/', $request->newpass)) {
            return response()->json(Notify::warning('Senha não atende aos critérios de segurança!
            Sua senha deve conter no minimo 08 caracteres com letras, números e símbolos'), 400);
        }

        $token = $request->token;
        $check = Guardian::checkToken($token);
        $user = User::where('token', $token)->where('passchange', true)->first();

        if (!$check || !$user) {
            return response()->json(Notify::warning('Token inválido ou expirado!'), 401);
        }

        $user->setAttribute('password', Hash::make($request->newpass));
        $user->token = null;
        $user->passchange = false;

        if ($user->update()) {
            Mail::to($user)->send(new ChangePassNotification());
            return response()->json(Notify::success('Senha alterada com sucesso!'), 200);
        }

        return response()->json(Notify::error('Falha ao cadastrar senha'), 500);
    }

    public function check(Request $request)
    {
        $token = $request->bearerToken();
        return Guardian::checkToken($token)
            ? response()->json(['token_valid' => true], 200)
            : response()->json(Notify::info('Login expirado, realize o login novamente...'), 401);
    }
}
