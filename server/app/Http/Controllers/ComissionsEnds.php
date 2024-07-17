<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Utils\Utils;
use App\Models\Organ;
use App\Utils\Notify;
use App\Models\Common;
use App\Utils\Uploads;
use App\Middleware\Data;
use App\Models\Comission;
use App\Models\ComissionEnd;
use Illuminate\Http\Request;

class ComissionsEnds extends Controller
{
    public function __construct()
    {
        parent::__construct(ComissionEnd::class, true, Common::MOD_COMISSIONS['module']);
    }

    public function save(Request $request)
    {
        $comission = Comission::find($request->comission);
        if (!$comission) {
            return response()->json(Notify::warning('Comissão não existe!'), 404);
        }
        if ($comission->status == Comission::STATUS_EXTINGUED) {
            return response()->json(Notify::info('Comissão já extinta!'), 200);
        }

        $upload   = new Uploads($request, ['document' => ['nullable' => true]]);

        if($request->id && $request->hasFile('document')) {
            $instance = $this->model::find($request->id);
            $upload->remove($instance->document);
        }

        $creation = $this->baseSave($upload->mergeUploads($request->all()));
        if ($creation->status() != 200) {
            return $creation;
        }

        $update = array_merge($comission->toArray(), [
            'end_term' => $request->end_term,
            'status' => Comission::STATUS_EXTINGUED,
        ]);

        return (new Comissions())->baseSave(array_merge(['id' => $request->comission], $update));
    }

    public function list(Request $request)
    {
        return $this->baseList(['organ', 'unit', 'name'], ['name']);
    }

    public function download(Request $request)
    {
        $ordinator = Comission::findOrFail($request->id);
        if ($ordinator && $ordinator->document) {
            return response()->download(storage_path('uploads' . '/' . $ordinator->document), $ordinator->name . '.pdf');
        }

        return response()->json(Notify::warning('Arquivo Indisponível'));
    }

    public function selects(Request $request)
    {
        $units = $request->key 
        ? Utils::map_select(Data::list(Unit::class, [$request->key => $request->search], ['name'])) 
        : Utils::map_select(Data::list(Unit::class));

        return Response()->json([
            'organs' => Utils::map_select(Data::list(Organ::class, order: ['name'])),
            'units' => $units,
            'types' => Comission::list_types(),
            'status' => Comission::list_status()
        ], 200);
    }
}
