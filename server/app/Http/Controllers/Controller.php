<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Utils\Notify;
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

    public function validateErros(string $model, ?array $data = []):?string
    {
        $validator = Validator::make($data, $model::validateFields(), $model::validateMsg());
        
        if($validator->fails()){
            Log::alert(json_encode($validator->errors(), JSON_UNESCAPED_UNICODE));
            return $validator->errors()->first();
        }

        return null;
    }
}

