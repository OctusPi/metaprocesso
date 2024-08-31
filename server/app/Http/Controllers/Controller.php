<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Utils\Utils;
use App\Utils\Notify;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Middlewares\Data;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller
{
    use AuthorizesRequests, ValidatesRequests;

    protected ?Model $model;
    protected ?string $access_check = null;
    protected bool $secutiry = true;

    /**
     * Construtor para o Controller base.
     *
     * @param string|null $model Classe do modelo.
     * @param string|null $access_check Permissão de acesso necessária.
     * @param bool $secutiry Habilita ou desabilita segurança de acesso.
     */
    public function __construct(?string $model = null, ?string $access_check = null, bool $secutiry = true)
    {
        $this->model = $model ? new $model() : null;
        $this->access_check = $access_check;
        $this->secutiry = $secutiry;
    }

    /**
     * Verifica a autorização do usuário e retorna a resposta apropriada.
     *
     * @param Request $request Objeto de requisição HTTP.
     * @return \Illuminate\Http\JsonResponse Resposta JSON.
     */
    public function index(Request $request)
    {
        if ($this->secutiry) {
            $user = $request->user();

            if (!$user) {
                return response()->json(Notify::error("Usuário não autenticado..."), 401);
            }

            if (!$this->access_check || !$user->tokenCan($this->access_check)) {
                return response()->json(Notify::error("Acesso não autorizado..."), 403);
            }

            if ($user->profile != User::PRF_ADMIN && !in_array($request->header('X-Custom-Header-Organ'), array_column($user->organs, 'id'))) {
                return response()->json(Notify::error("Acesso não autorizado ao Órgão."), 403);
            }
        }

        return response()->json('success');
    }

    /**
     * Verifica a autenticação do usuário.
     *
     * @param Request $request Objeto de requisição HTTP.
     * @return void
     */
    public function check_auth(Request $request)
    {
        if ($this->index($request)->status() != 200) {
            die();
        }
    }

    /**
     * Realiza a operação de salvar um registro usando a classe Data.
     *
     * @param Request $request Objeto de requisição HTTP.
     * @param array $overrite Dados para sobrescrever.
     * @return \Illuminate\Http\JsonResponse Resposta JSON.
     */
    public final function base_save(Request $request, array $overrite = [])
    {
        $this->check_auth($request);

        $data = Data::save($this->model, array_merge($request->all(), $overrite));

        return response()->json($data->notify, $data->code);
    }

    /**
     * Obtém os detalhes de um registro específico.
     *
     * @param Request $request Objeto de requisição HTTP.
     * @param array|null $with Relacionamentos para carregar.
     * @return \Illuminate\Http\JsonResponse Resposta JSON.
     */
    public final function base_details(Request $request, ?array $with = [])
    {
        $this->check_auth($request);

        $data = Data::findOne($this->model, ['id' => $request->id], with: $with);

        return response()->json($data, $data ? 200 : 404);
    }

    /**
     * Lista registros de acordo com os parâmetros fornecidos.
     *
     * @param Request $request Objeto de requisição HTTP.
     * @param array $search Parâmetros de busca.
     * @param array|null $order Ordem dos resultados.
     * @param array|null $with Relacionamentos para carregar.
     * @param array|null $between Intervalo de dados para filtragem.
     * @param bool|null $objects Filtrar por coluna do tipo json.
     * @return \Illuminate\Http\JsonResponse Resposta JSON.
     */
    public final function base_list(Request $request, array $search, ?array $order = null, ?array $with = null, ?array $between = null, ?array $objects = null)
    {
        $this->check_auth($request);

        try {
            $mapped = Utils::map_search($search, array_merge($request->route()->parameters(), $request->all()));
            $data = Data::find($this->model, $mapped, $order, $with, $between, $objects);

            return response()->json($data, 200);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json(Notify::error('Falha ao recuperar dados...'), 500);
        }
    }

    /**
     * Exclui um registro após verificar a senha do usuário.
     *
     * @param Request $request Objeto de requisição HTTP.
     * @return \Illuminate\Http\JsonResponse Resposta JSON.
     */
    public final function base_delete(Request $request)
    {
        $this->check_auth($request);

        if (!password_verify($request->password, $request->user()->getAttribute('password'))) {
            return response()->json(Notify::warning('Senha de confirmação incorreta!'), 401);
        }

        $data = Data::delete($this->model, $request->id);

        return response()->json($data->notify, $data->code);
    }

    /**
     * Exclui rapidamente um registro.
     *
     * @param Request $request Objeto de requisição HTTP.
     * @return \Illuminate\Http\JsonResponse Resposta JSON.
     */
    public final function fast_delete(Request $request)
    {
        $this->check_auth($request);

        $data = Data::delete($this->model, $request->id);

        return response()->json($data->notify, $data->code);
    }

    /**
     * Salva um registro.
     *
     * @param Request $request Objeto de requisição HTTP.
     * @return \Illuminate\Http\JsonResponse Resposta JSON.
     */
    public function save(Request $request)
    {
        return $this->base_save($request);
    }

    /**
     * Obtém detalhes de um registro.
     *
     * @param Request $request Objeto de requisição HTTP.
     * @return \Illuminate\Http\JsonResponse Resposta JSON.
     */
    public function details(Request $request)
    {
        return $this->base_details($request);
    }

    /**
     * Lista registros.
     *
     * @param Request $request Objeto de requisição HTTP.
     * @return \Illuminate\Http\JsonResponse Resposta JSON.
     */
    public function list(Request $request)
    {
        return $this->base_list($request, []);
    }

    /**
     * Exclui um registro.
     *
     * @param Request $request Objeto de requisição HTTP.
     * @return \Illuminate\Http\JsonResponse Resposta JSON.
     */
    public function delete(Request $request)
    {
        return $this->base_delete($request);
    }

    /**
     * Retorna seleções específicas.
     *
     * @param Request $request Objeto de requisição HTTP.
     * @return array Array de seleções.
     */
    public function selects(Request $request)
    {
        return [];
    }

    public function generate(Request $request)
    {
        $api_key = getenv('OPENIA_KEY');
        $client = new Client();
        $url = 'https://api.openai.com/v1/chat/completions';
        $data = [
            'model' => 'gpt-4',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $request->payload
                ]
            ],
            'temperature' => 0.7
        ];

        try {

            $resp = $client->post($url, [
                'headers' => [
                    'Authorization' => "Bearer $api_key",
                    'Content-Type' => 'application/json'
                ],
                'json' => $data
            ]);

            return Response()->json(json_decode($resp->getBody(), true), 200);

        } catch (\Exception $e) {
            Log::alert('Falha ao receber dados da API: ' . $e->getMessage());
            return Response()->json(Notify::warning('Falha ao receber dados da API'), 404);
        }
    }
}
