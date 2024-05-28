<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\User;
use App\Utils\Utils;
use App\Models\Organ;
use App\Utils\Notify;
use App\Utils\Uploads;
use App\Middleware\Data;
use App\Models\Comission;
use App\Security\Guardian;
use Illuminate\Http\Request;

class Comissions extends Controller
{
    public function __construct()
    {
        parent::__construct(User::MOD_MANAGEMENT);
        Guardian::validateAccess($this->module_id);
    }

    public function save(Request $request)
    {
        //upload file
        $upload = new Uploads($request, ['document' => ['nullable' => true]]);
        $values = $upload->mergeUploads($request->all());

        return $this->baseSave(Comission::class, $values);
    }

    public function update(Request $request)
    {
        if (isset($_FILES['document'])) {
            $comission = Comission::findOrFail($request->id);
            $upload = new Uploads($request, ['document' => ['nullable' => true]]);
            $upload->remove($comission->document);
            $values = $upload->mergeUploads($request->all());

            return $this->baseUpdate(Comission::class, $request->id, $values);
        }

        return $this->baseUpdate(Comission::class, $request->id, $request->all());
    }

    public function delete(Request $request)
    {
        return $this->baseDelete(Comission::class, $request->id, $request->password);
    }

    public function list(Request $request)
    {
        $search = ['organ', 'unit', 'name'];
        return $this->baseList(Comission::class, $search, $request->all(), ['name'], ['organ', 'unit']);
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

        return $this->baseDetails(Comission::class, $request->id);
    }

    public function download(Request $request)
    {
        $comission = Comission::findOrFail($request->id);
        if ($comission && $comission->document) {
            return response()->download(storage_path('uploads' . '/' . $comission->document), $comission->name . '.pdf');
        }

        return response()->json(Notify::warning('Arquivo Indisponível'));
    }

    public function selects(Request $request)
    {
        $units = $request->key ? Utils::map_select(Data::list(Unit::class, [
            [
                'column' => $request->key,
                'operator' => '=',
                'value' => $request->search,
                'mode' => 'AND'
            ]
        ], ['name'])) : Utils::map_select(Data::list(Unit::class));

        return Response()->json([
            'organs' => Utils::map_select(Data::list(Organ::class, order: ['name'])),
            'units' => $units,
            'types' => Comission::list_types(),
            'status' => Comission::list_status()
        ], 200);
    }
}
