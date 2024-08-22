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

        if($request->id && $request->hasFile('document')) {
            $instance = $this->model::find($request->id);
            $upload->remove($instance->document);
        }

        return $this->base_save($request, $upload->mergeUploads([]));
    }

    public function details(Request $request)
    {
        if ($request->query('display') == 1) {

            $instance = Comission::with(['organ', 'unit'])->find($request->id);

            if (!$instance) {
                return Response()->json(Notify::warning('Registro não localizado!'), 404);
            }

            $displayMode = array_merge($instance->toArray(), [
                'type' => Comission::get_type($instance->type),
                'status' => Comission::get_status($instance->status),
            ]);

            return Response()->json($displayMode, 200);
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
        $units = Utils::map_select(Data::find(Unit::class, [
            [
                'column' => 'organ',
                'operator' => '=',
                'mode' => 'AND',
                'value' => $request->header('X-Custom-Header-Organ'),
            ]
        ], ['name']));

        return Response()->json([
            'units' => $units,
            'types' => Comission::list_types(),
            'status' => Comission::list_status()
        ], 200);
    }
}
