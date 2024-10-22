<?php

namespace App\Http\Controllers;

use App\Http\Middlewares\Data;
use App\Models\Comission;
use App\Models\Pca;
use App\Models\User;
use App\Utils\Notify;
use App\Utils\Utils;
use Illuminate\Http\Request;

class Pcas extends Controller
{
    public function __construct()
    {
        parent::__construct(Pca::class, User::MOD_PCA['module']);
    }

    public function list(Request $request)
    {
        return $this->base_list(
            $request,
            ['reference_year', 'status', 'comission_id'],
            ['reference_year', 'desc'],
            ['comission'],
        );
    }

    public function save(Request $request)
    {
        $comission = Data::findOne(new Comission(), ['id' => $request->comisison_id]);

        if (!$comission) {
            return response()->json(Notify::warning('Comissão não encontrada'), 404);
        }

        return $this->base_save($request, [
            'ip' => $request->ip(),
            'author_id' => $request->user()->id,
            'comission_members' => $comission->comissionmembers
        ]);
    }

    public function selects(Request $request)
    {
        return response()->json([
            'status' => Pca::list_status(),
            'comissions' => Utils::map_select(
                Data::find(new Comission(), ['status' => Comission::STATUS_ACTIVE], ['name'])
            ),
        ]);
    }
}
