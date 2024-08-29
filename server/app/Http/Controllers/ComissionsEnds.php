<?php

namespace App\Http\Controllers;

use App\Http\Middlewares\Data;
use App\Models\Comission;
use App\Models\ComissionEnd;
use App\Models\Unit;
use App\Models\User;
use App\Utils\Notify;
use App\Utils\Uploads;
use App\Utils\Utils;
use Illuminate\Http\Request;

class ComissionsEnds extends Controller
{
    public function __construct()
    {
        parent::__construct(ComissionEnd::class, User::MOD_COMISSIONS['module']);
    }

    public function list(Request $request)
    {
        return $this->base_list(
            $request,
            ['name', 'description'],
            ['end_term'],
            ['organ', 'unit', 'comission']
        );
    }

    public function save(Request $request)
    {
        $comission = Data::findOne(new Comission(), ['id' => $request->comission]);

        if (!$comission) {
            return response()->json(Notify::warning('Comissão não existe'), 404);
        }

        if ($comission->status == Comission::STATUS_EXTINGUED) {
            return response()->json(Notify::warning('Comissão já foi extinta'), 401);
        }

        $upload = new Uploads($request, ['document' => ['nullable' => true]]);

        if ($request->id && $request->hasFile('document')) {
            $instance = $this->model::find($request->id);
            $upload->remove($instance->document);
        }

        $comission->status = Comission::STATUS_EXTINGUED;
        $comission->end_term = $request->end_term;

        $comission->save();

        return $this->base_save($request, $upload->mergeUploads([]));
    }

    public function download(Request $request)
    {
        $ended = ComissionEnd::with('comission')->find($request->id);
        $comission = $ended->comission->name;

        if ($ended && $ended->document) {
            return response()->download(storage_path("uploads/$ended->document"), "$comission.pdf");
        }

        return response()->json(Notify::warning('Arquivo Indisponível'));
    }

    public function selects(Request $request)
    {
        return Response()->json([
            'units' => Utils::map_select(Data::find(new Unit(), order: ['name'])),
        ], 200);
    }
}
