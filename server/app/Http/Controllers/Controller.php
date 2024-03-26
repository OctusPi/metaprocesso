<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Utils\Notify;
use App\Security\Guardian;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected int $module_id;

    public function __construct(int $module_id = User::MOD_INI){
        $this->module_id = $module_id;
    }

    protected function navigation(User $user)
    {
        return Response()->json(['navigation' => $user->modules], 200);
    }

    public function authcheck()
    {
        return Guardian::checkToken() && Guardian::checkAccess($this->module_id)
        ? Response()->json(['token_valid' => true], 200)
        : Response()->json(Notify::warning('Sessão Expirada, realize o login novamente'), 403);
    }


}
