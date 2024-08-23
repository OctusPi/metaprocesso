<?php

namespace App\Http\Controllers;

use App\Http\Middlewares\Data;
use App\Models\Comission;
use App\Models\Unit;
use App\Models\User;
use App\Utils\Notify;
use App\Utils\Uploads;
use App\Utils\Utils;
use Illuminate\Http\Request;

class Comissions extends Controller
{
    public function __construct()
    {
        parent::__construct(Comission::class, User::MOD_COMISSIONS['module']);
    }

    public function list(Request $request)
    {
        return $this->base_list(
            $request,
            ['name', 'type', 'description'],
            ['name'],
            ['organ', 'unit'],
            organ: true
        );
    }

    public function save(Request $request)
    {
        $upload = new Uploads($request, ['document' => ['nullable' => true]]);

        if ($request->id && $request->hasFile('document')) {
            $instance = $this->model::find($request->id);
            $upload->remove($instance->document);
        }

        return $this->base_save($request, $upload->mergeUploads([]));
    }

    public function details(Request $request)
    {
        if ($request->query('display') == 1) {
            return Data::findOne(Comission::class, ['id' => $request->id], with: ['organ', 'unit']);
        }

        return $this->base_details($request);
    }

    public function download(Request $request)
    {
        $comission = Comission::find($request->id);

        if ($comission && $comission->document) {
            return response()->download(storage_path("uploads/$comission->document"), "$comission->name.pdf");
        }

        return response()->json(Notify::warning('Arquivo Indisponível'));
    }

    public function selects(Request $request)
    {
        return Response()->json([
            'units' => Utils::map_select(Data::find(Unit::class, order: ['name'])),
            'types' => Comission::list_types(),
            'status' => Comission::list_status()
        ], 200);
    }
}
