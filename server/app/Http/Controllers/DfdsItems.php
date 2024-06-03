<?php

namespace App\Http\Controllers;

use App\Models\Dfd;
use App\Models\DfdItem;
use App\Models\Dotation;
use App\Models\Program;
use App\Models\Unit;
use App\Models\User;
use App\Models\Organ;
use App\Utils\Notify;
use App\Utils\Utils;
use GuzzleHttp\Client;
use App\Middleware\Data;
use App\Models\Comission;
use App\Models\Demandant;
use App\Models\Ordinator;
use App\Security\Guardian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Dfds extends Controller
{
    public function __construct()
    {
        parent::__construct(DfdItem::class, User::MOD_DFDS);
        Guardian::validateAccess($this->module_id);
    }

    public function list(Request $request)
    {
        return $this->baseList(['name'], ['name'], ['organ', 'unit']);
    }
}
