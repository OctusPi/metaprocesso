<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class Home extends Controller
{
    public function __construct(){
        parent::__construct(null, User::MOD_INI['module']);
    }
}
