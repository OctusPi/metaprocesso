<?php

namespace App\Http\Controllers;

use App\Utils\Utils;
use App\Utils\Notify;
use GuzzleHttp\Client;
use App\Middleware\Data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected ?string $model;
    protected bool $secutiry = true;
    protected ?string $access_check = null;

    public function __construct(?string $model = null, bool $secutiry = true, ?string $access_check = null)
    {
        $this->model = $model;
        $this->access_check = $access_check;
        $this->secutiry = $secutiry;
    }

    public function index(Request $request)
    {
        if ($this->secutiry) {

            $user = $request->user();

            if (!$user) {
                return response()->json(Notify::error("Usuário não localizado..."), 401);
            }

            if ($this->access_check == null || !$user->tokenCan($this->access_check)) {
                return response()->json(Notify::error("Acesso não autorizado..."), 403);
            }
        }

        return response()->json('success');
    }

    public function validateCheck(string $model, ?array $data = [])
    {
        return Validator::make($data, $model::validateFields(), $model::validateMsg());
    }

    public function validateErros(string $model, ?array $data = []): ?string
    {
        if (method_exists($model, 'validateFields') && method_exists($model, 'validateMsg')) {
            $validator = Validator::make($data, $model::validateFields($data['id'] ?? null), $model::validateMsg());
            if ($validator->fails()) {
                return $validator->errors()->first();
            }
        }

        return null;
    }

    public function baseSave(array $requests)
    {

        $validateErros = $this->validateErros($this->model, $requests);
        if ($validateErros) {
            return response()->json(Notify::warning($validateErros), 400);
        }

        try {
            

            if (isset($requests['id']) && $requests['id'] > 0) {

                $instance = (new $this->model())->find($requests['id']);
                if ($instance && $instance->update($requests)) {
                    return response()->json(Notify::success("Registro salvo com sucesso!"), 200);
                }
                
            } else {
                $instance = new $this->model($requests);
                if ($instance->save()) {
                    return Response()->json(Notify::success("Registro realizado com sucesso!"), 200);
                }
            }

            return Response()->json(Notify::error("Falha ao salvar registro!"), 500);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return Response()->json(Notify::error("Falha ao salvar registro!"), 500);
        }
    }

    public function baseSaveInstance(array $requests)
    {
        $exec = [
            'instance' => null,
            'error' => null
        ];

        $validateErros = $this->validateErros($this->model, $requests);
        if ($validateErros) {
            $exec['error'] = Notify::warning($validateErros);
        }

        try {
            if (isset($requests['id']) && $requests['id'] > 0) {
                
                $instance = (new $this->model())->find($requests['id']);
                if ($instance && $instance->update($requests)) {
                    $exec['instance'] = $instance;
                }

            }else{
                $instance = new $this->model($requests);
                if ($instance->save()) {
                    $exec['instance'] = $instance;
                }
            }
            
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return (object) $exec;
    }

    public function baseDelete(?int $id, string $pass)
    {
        $user = Auth::user();
        if (!$user || !password_verify($pass, $user->getAttribute('password'))) {
            return Response()->json(Notify::warning('Senha de confirmação inválida!'), 401);
        }

        try {
            $instance = $this->model::where('id', $id)->first();
            if ($instance->delete()) {
                return Response()->json(Notify::success('Registro excluído com sucesso!'), 200);
            }
            return Response()->json(Notify::warning('Dados para exclusão nao localizado!'), 404);
        } catch (\Exception $e) {
            return Response()->json(Notify::error('Ação não permitida, registro referenciado em outros contextos!'), 500);
        }

    }

    public function baseList(array $search, ?array $order = null, ?array $with = null, ?array $between = null)
    {
        try {
            $search = Utils::map_search($search, array_merge(Request()->all(), Request()->route()->parameters()));
            $query = Data::list($this->model, $search, $order, $with, $between);
            return Response()->json($query ?? [], 200);
        } catch (\Throwable $th) {
            return Response()->json(Notify::error($th->getMessage() . 'Falha ao recuperar dados!'), 500);
        }
    }

    public function baseDetails(?int $id, ?array $with = [])
    {
        $instance = $this->model::where("id", $id)->with($with)->first();
        if (!$instance) {
            return Response()->json(Notify::warning('Registro não localizado!'), 404);
        }

        return Response()->json($instance->toArray(), 200);
    }

    public function save(Request $request)
    {
        return $this->baseSave($request->all());
    }

    public function delete(Request $request)
    {
        return $this->baseDelete($request->id, $request->password);
    }

    public function details(Request $request)
    {
        return $this->baseDetails($request->id);
    }

    public function fastdestroy(Request $request)
    {
        try {
            $instance = $this->model::where('id', $request->id)->first();
            if ($instance->delete()) {
                return Response()->json(Notify::success('Registro excluído com sucesso!'), 200);
            }
            return Response()->json(Notify::warning('Dados para exclusão nao localizado!'), 404);
        } catch (\Exception $e) {
            return Response()->json(Notify::error('Ação não permitida, registro referenciado em outros contextos!'), 500);
        }
    }

    public function generate(Request $request)
    {
        $api_key = getenv('OPENIA_KEY');
        $client = new Client();
        $url = 'https://api.openai.com/v1/completions';
        $data = [
            'model' => 'gpt-3.5-turbo-instruct',
            'prompt' => $request->payload,
            'max_tokens' => 512
        ];

        try {

            $resp = $client->post($url, [
                'headers' => [
                    'Authorization' => "Bearer $api_key",
                    'Content-Type' => 'application/json'
                ],
                'json' => $data
            ]);

            return Response()->json(json_decode($resp->getBody()), 200);

        } catch (\Exception $e) {
            Log::alert('Falha ao receber dados da API: ' . $e->getMessage());
            return Response()->json(Notify::warning('Falha ao receber dados da API'), 400);
        }
    }
}

