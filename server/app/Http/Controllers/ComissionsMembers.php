<?php

namespace App\Http\Controllers;

use App\Utils\Notify;
use App\Models\Common;
use App\Utils\Uploads;
use App\Middleware\Data;
use App\Models\Comission;
use Illuminate\Http\Request;
use App\Models\ComissionMember;

class ComissionsMembers extends Controller
{
    public function __construct()
    {
        parent::__construct(ComissionMember::class, true, Common::MOD_COMISSIONS['module']);
    }

    public function save(Request $request)
    {
        $comission = Data::find(Comission::class, ['id' => $request->comission]);

        if($comission){

            //upload file
            $upload = new Uploads($request, ['document' => ['nullable' => true]]);
            $values = $upload->mergeUploads($request->all());

            if($request->id && $request->hasFile('document')) {
                $instance = $this->model::find($request->id);
                $upload->remove($instance->document);
            }

            return $this->baseSave(array_merge($values, [
                'organ'     => $comission->organ,
                'unit'      => $comission->unit,
                'comission' => $comission->id
            ]));
        }

        return Response()->json(Notify::warning('Comissão inexistente'), 404);
        
    }

    public function list(Request $request)
    {
        return $this->baseList(['status', 'responsibility', 'name'], ['name']);
    }

    public function download(Request $request)
    {
        $ordinator = ComissionMember::findOrFail($request->id);
        if ($ordinator && $ordinator->document) {
            return response()->download(storage_path('uploads' . '/' . $ordinator->document), $ordinator->name . '.pdf');
        }

        return response()->json(Notify::warning('Arquivo Indisponível'));
    }

    public function selects(Request $request)
    {
        return Response()->json([
            'responsibilities' => ComissionMember::list_responsabilities(),
            'status' => ComissionMember::list_status(),
        ], 200);
    }
}
