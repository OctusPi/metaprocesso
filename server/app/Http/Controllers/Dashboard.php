<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Security\Guardian;
use Illuminate\Http\Request;

class Dashboard extends Controller
{
    public function __construct()
    {
        parent::__construct(User::MOD_INI);
    }

    public function save(Request $request)
    {
        
    }
    public function update(Request $request)
    {

    }

    public function destroy(Request $request)
    {

    }

    public function list(Request $request)
    {
        
    }
}
