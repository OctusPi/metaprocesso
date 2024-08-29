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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class Users extends Controller
{
    /**
     * Construtor do Controller de Usuários.
     */
    public function __construct()
    {
        parent::__construct(User::class, User::MOD_USERS['module']);
    }

    /**
     * Lista os usuários de acordo com os parâmetros de busca.
     *
     * @param Request $request Objeto de requisição HTTP.
     * @return \Illuminate\Http\JsonResponse Resposta JSON.
     */
    public function list(Request $request)
    {
        $organs = array_column($request->user()->organs, 'id');
        $s_objs = Utils::map_search_obj($organs, 'organs', 'id');

        return $this->base_list(
            $request,
            ['name', 'profile', 'email'],
            ['name'],
            objects:$s_objs
        );
    }

    /**
     * Salva um usuário, criando um novo ou atualizando um existente.
     *
     * @param Request $request Objeto de requisição HTTP.
     * @return \Illuminate\Http\JsonResponse Resposta JSON.
     */
    public function save(Request $request)
    {
        // Atualização de usuário existente
        if ($request->id) {
            return $this->base_save($request);
        }

        // Criação de novo usuário
        $pass = Str::random(8);
        $user = $this->model->fill($request->all());
        $user->username = $request->email;
        $user->password = bcrypt($pass);  // Segurança: criptografar a senha

        if ($user->save()) {
            // Envio do e-mail de boas-vindas
            Mail::to($user)->send(new Wellcome($user, $pass));
            return response()->json(Notify::success("Usuário cadastrado com sucesso"), 200);
        }

        return response()->json(Notify::warning("Usuário já cadastrado no sistema"), 400);
    }

    /**
     * Retorna dados de seleção para uso em formulários ou filtros.
     *
     * @param Request $request Objeto de requisição HTTP.
     * @return \Illuminate\Http\JsonResponse Resposta JSON.
     */
    public function selects(Request $request)
    {
        $profiles = array_filter(User::list_profiles(), function ($value, $key) use ($request) {
            return $key >= $request->user()->profile;
        }, ARRAY_FILTER_USE_BOTH);

        // Mapeia os perfis em um formato adequado para seleção
        $profiles = array_map(fn($key, $value) => ['id' => $key, 'title' => $value], array_keys($profiles), $profiles);

        try {
            return response()->json([
                'profiles' => $profiles,
                'organs' => Utils::map_select(Data::find(new Organ(), order: ['name'])),
                'units' => Utils::map_select(Data::find(new Unit(), order: ['name'])),
                'sectors' => Utils::map_select(Data::find(new Sector(), order: ['name'])),
                'status' => User::list_status(),
                'modules' => $request->user()->profile != User::PRF_ADMIN
                    ? $request->user()->modules
                    : User::list_modules(),
            ], 200);
        } catch (Exception $th) {
            Log::error($th->getMessage());
            return response()->json(Notify::error('Falha ao recuperar dados!'), 500);
        }
    }
}
