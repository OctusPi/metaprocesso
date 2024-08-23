<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Utils\Utils;
use App\Utils\Notify;
use Illuminate\Http\Request;
use App\Http\Middlewares\Data;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller
{
    use AuthorizesRequests, ValidatesRequests;

    protected ?Model $model;
    protected bool $secutiry = true;
    protected ?string $access_check = null;

    public function __construct(?string $model = null, ?string $access_check = null, bool $secutiry = true)
    {
        $this->model = !is_null($model) ? new $model() : null;
        $this->access_check = $access_check;
        $this->secutiry = $secutiry;
    }

    public function index(Request $request)
    {
        if ($this->secutiry) {

            $user = $request->user();

            if (!$user) {
                return response()->json(Notify::error("Usuário não autenticado..."), 401);
            }

            if ($this->access_check == null || !$user->tokenCan($this->access_check)) {
                return response()->json(Notify::error("Acesso não autorizado..."), 403);
            }

            if($user->profile != User::PRF_ADMIN && !in_array($request->header('X-Custom-Header-Organ'), array_column($user->organs, 'id'))){
                return response()->json(Notify::error("Acesso não autorizado ao Órgão."), 403);
            }

        }

        return response()->json('success');
    }

    private function check_auth(Request $request)
    {
        if ($this->index($request)->status() != 200) {
            die();
        }
    }

    public function validate(?array $data = []): ?string
    {
        if (method_exists($this->model, 'rules') && method_exists($this->model, 'messages')) {
            $validator = Validator::make($data, $this->model->rules(), $this->model->messages());
            if ($validator->fails()) {
                return $validator->errors()->first();
            }
        }

        return null;
    }

    public final function base_save(Request $request, array $overrite = [])
    {
        $this->check_auth($request);

        $dataModel = array_merge(
            ['organ' => $request->header('X-Custom-Header-Organ')],
            $request->all(),
            $overrite,
        );

        $this->model->fill($dataModel);
        $validate = $this->validate($dataModel);

        if (!is_null($validate)) {
            return response()->json(Notify::warning($validate), 401);
        }

        try {
            if ($request->id) {
                $this->model = $this->model->find($request->id);
                $this->model->fill($dataModel);
            }

            $this->model->save();

            Log::info($this->model);

            return response()->json(Notify::success('Dados salvos com sucesso...'), 200);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json(Notify::error('Falha ao gravar dados...'), 500);
        }

    }

    public final function base_details(Request $request, ?array $with = [])
    {
        $this->check_auth($request);

        $query = $this->model->with($with)->find($request->id);

        if ($query) {
            return response()->json($query->toArray(), 200);
        }

        return response()->json(Notify::warning('Registro não localizado'), 404);
    }

    public final function base_list(
        Request $request,
        array $search,
        ?array $order = null,
        ?array $with = null,
        ?array $between = null,
        ?bool $organ = false,
    ) {
        $this->check_auth($request);

        try {
            $mapped = Utils::map_search(
                $search,
                array_merge(
                    $request->route()->parameters(),
                    $request->all(),
                ),
            );

            if ($organ) {
                $mapped[] = [
                    'column' => 'organ',
                    'operator' => '=',
                    'mode' => 'AND',
                    'value' => $request->header('X-Custom-Header-Organ')
                ];
            }

            $query = Data::find($this->model::class, $mapped, $order, $with, $between);

            return response()->json($query, 200);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json(Notify::error('Falha ao recuperar dados...'), 500);
        }
    }

    public final function base_delete(Request $request)
    {
        $this->check_auth($request);

        if (!password_verify($request->password, $request->user()->getAttribute('password'))) {
            return response()->json(Notify::warning('Senha de confirmação incorresta!'), 401);
        }

        try {
            $this->model->where('id', $request->id)->delete();
            return response()->json(Notify::success('Registro apagado com sucesso...'), 200);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json(Notify::error('Registro não pode ser apagado, pois é referenciado em outro contexto...'), 500);
        }
    }

    public final function fast_delete(Request $request)
    {
        $this->check_auth($request);

        try {
            $this->model->where('id', $request->id)->delete();
            return response()->json(Notify::success('Registro apagado com sucesso...'), 200);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json(Notify::error('Registro não pode ser apagado, pois é referenciado em outro contexto...'), 500);
        }
    }

    public function save(Request $request)
    {
        return $this->base_save($request);
    }

    public function details(Request $request)
    {
        return $this->base_details($request);
    }

    public function list(Request $request)
    {
        return $this->base_list($request, []);
    }

    public function delete(Request $request)
    {
        return $this->base_delete($request);
    }

    public function selects(Request $request)
    {
        return [];
    }
}
