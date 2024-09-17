<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Utils\Notify;
use Illuminate\Http\Request;

class ProposalsSupplier extends Controller
{
    public function __construct()
    {
        parent::__construct(Proposal::class, null, false);
    }

    public function check(Request $request)
    {
        $proposal = Proposal::firstWhere('token', '=', $request->token);
        return response()->json(Notify::success($request->token));
    }
}
