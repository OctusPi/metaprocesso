<?php

namespace App\Http\Controllers;

use App\Http\Middlewares\Data;
use App\Models\User;
use App\Utils\Notify;
use App\Utils\Uploads;
use App\Models\Comission;
use Illuminate\Http\Request;
use App\Models\ComissionMember;
use Log;

class ComissionMembers extends Controller
{
    public function __construct()
    {
        parent::__construct(ComissionMember::class, User::MOD_COMISSIONS['module']);
    }

    public function save(Request $request)
    {
        $comission = Data::findOne(Comission::class, ['id' => $request->comission]);

        if (!$comission) {
            return response()->json(Notify::warning('Comissão inexistente'), 404);
        }

        $upload = new Uploads($request, ['document' => ['nullable' => true]]);

        if ($request->id && $request->hasFile('document')) {
            $instance = $this->model::find($request->id);
            $upload->remove($instance->document);
        }

        return $this->base_save($request, $upload->mergeUploads([
            'organ' => $comission->organ,
            'unit' => $comission->unit,
            'comission' => $comission->id
        ]));
    }

    public function list(Request $request)
    {
        $comission = Data::findOne(Comission::class, ['id' => $request->comission]);

        if (!$comission) {
            return response()->json(Notify::warning('Comissão inexistente'), 404);
        }

        return $this->base_list(
            $request,
            ['comission', 'status', 'responsibility', 'name'],
            ['name'],
            organ: true,
        );
    }

    public function download(Request $request)
    {
        $member = ComissionMember::find($request->id);
        if ($member && $member->document) {
            return response()->download(storage_path("uploads/$member->document"), "$member->name.pdf");
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
