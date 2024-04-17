<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Utils\Utils;
use App\Utils\Notify;
use App\Middleware\Data;
use App\Security\Guardian;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected int $module_id;
    protected ?User $user_loged;

    public function __construct(int $module_id = User::MOD_INI){
        $this->module_id = $module_id;
        $this->user_loged = Guardian::getUser();
    }

    public function index(){
        return response()->json('success', 200);
    }

    public function validateCheck(string $model, ?array $data = [])
    {
        return Validator::make($data, $model::validateFields(), $model::validateMsg());
    }

    public function validateErros(string $model, ?array $data = [], ?int $id = null):?string
    {
        $validator = Validator::make($data, $model::validateFields($id), $model::validateMsg());
        if($validator->fails()){
            return $validator->errors()->first();
        }

        return null;
    }

    public function baseSave(string $model, array $requests)
    {
        $validateErros = $this->validateErros($model, $requests);
        if ($validateErros) {
            return response()->json(Notify::warning($validateErros), 400);
        }

        try {
            $instance = new $model($requests);
            if ($instance->save()) {
                return Response()->json(Notify::success("Cadastro realizado com sucesso!"), 200);
            }
        } catch (\Exception $e) {
            return Response()->json(Notify::error("Falha ao realizar cadastro!"), 500);
        }

    }

    public function baseUpdate(string $model, ?int $id, array $requests)
    {
        $validateErros = $this->validateErros($model, $requests, $id);
        if ($validateErros) {
            return response()->json(Notify::warning($validateErros), 400);
        }

        try {
            $instance = $model::findOrFail($id);
            if ($instance->update($requests)) {
                return Response()->json(Notify::success("Registro atualizado com sucesso!"), 200);
            }
        } catch (\Exception $e) {
            Log::alert($e->getMessage());
            return Response()->json(Notify::error("Falha ao atualizar registro!"), 500);
        }
    }

    public function baseDelete(string $model, ?int $id, string $pass)
    {
        if (!password_verify($pass, $this->user_loged->password)) {
            return Response()->json(Notify::warning('Senha de confirmação inválida!'), 401);
        }

        try {
            $instance = $model::where('id', $id)->first();
            if ($instance->delete()) {
                return Response()->json(Notify::success('Registro excluído com sucesso!'), 200);
            }
            return Response()->json(Notify::warning('Dados para exclusão nao localizado!'), 404);
        } catch (\Exception $e) {
            return Response()->json(Notify::error('Ação não permitida, registro referenciado em outros contextos!'), 500);
        }
        
    }

    public function baseList(string $model, array $search, array $requests)
    {
        try {
            $search = Utils::map_search($search, $requests);
            $query = Data::list($model, $search);
            return Response()->json($query ?? [], 200);
        } catch (\Throwable $th) {
            return Response()->json(Notify::error($th->getMessage() . 'Falha ao recuperar dados!'), 500);
        }
    }

    public function baseDetails(string $model, ?int $id)
    {
        $instance = $model::where("id", $id)->first();
        if (!$instance) {
            return Response()->json(Notify::warning('Registro não localizado!'), 404);
        }

        return Response()->json($instance->toArray(), 200);
    }
}

