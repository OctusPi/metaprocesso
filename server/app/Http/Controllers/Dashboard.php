<?php

namespace App\Http\Controllers;


use App\Models\Common;
use Illuminate\Http\Request;

class Dashboard extends Controller
{
    public function __construct()
    {
        parent::__construct(Dashboard::class, true, Common::MOD_INI['module']);
    }
}