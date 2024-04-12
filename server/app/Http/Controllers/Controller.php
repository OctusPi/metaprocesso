<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Security\Guardian;
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
}
