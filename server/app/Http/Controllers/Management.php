<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Utils\Utils;
use App\Utils\Notify;
use App\Mail\Wellcome;
use App\Middleware\Data;
use App\Security\Guardian;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
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
        $pass = Str::random(8);
        $user = new User($request->all());
        $user->username = $request->get("email");
        $user->password = Hash::make($pass);

        if($user->save()){
            Mail::to($user)->send(new Wellcome($user, $pass));
            return Response()->json(Notify::success("Usuário cadastrado com sucesso"), 200);
        }

        return Response()->json(Notify::warning("Usuário já cadastrado no sistema"), 400);
    }
    
    public function update(Request $request)
    {

    }

    public function delete(Request $request)
    {
        if(!password_verify($request->password, $this->user_loged->password)) {
            return Response()->json(Notify::warning('Senha de confirmação inválida!'), 401);
        }

        $user = User::where('id', $request->get('id'))->first();
        if($user) {
    
            if($user->delete()){
               return Response()->json(Notify::success('Usuário excluído com sucesso'), 200);
            }

            return Response()->json(Notify::warning('Ação não permitida, usuário referenciado em outros contextos!'), 400);
        }

        return Response()->json(Notify::warning('Dados para exclusão nao localizado!'), 404);
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
