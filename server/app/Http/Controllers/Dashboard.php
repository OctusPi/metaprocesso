<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Security\Guardian;
use Illuminate\Http\Request;

class Dashboard extends Controller
{
    public function __construct()
    {
        parent::__construct(module_id:User::MOD_INI);
    }
}
