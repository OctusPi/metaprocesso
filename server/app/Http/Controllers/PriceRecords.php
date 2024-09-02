<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PriceRecord;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PriceRecords extends Controller
{
    public function __construct()
    {
        parent::__construct(PriceRecord::class, User::MOD_PRICERECORDS['module']);
    }
}
