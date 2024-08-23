<?php

namespace App\Http\Controllers;

use App\Http\Middlewares\Data;
use App\Models\Ordinator;
use App\Models\Unit;
use App\Models\User;
use App\Utils\Notify;
use App\Utils\Uploads;
use App\Utils\Utils;
use Illuminate\Http\Request;

class Ordinators extends Controller
{
    public function __construct()
    {
        parent::__construct(Ordinator::class, User::MOD_ORDINATORS['module']);
    }

    public function list(Request $request)
    {
        return $this->base_list(
            $request,
            ['unit', 'name', 'status'],
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

    public function download(Request $request)
    {
        $ordinator = Ordinator::find($request->id);

        if ($ordinator && $ordinator->document) {
            return response()->download(storage_path("uploads/{$ordinator->document}"), "{$ordinator->name}.pdf");
        }

        return response()->json(Notify::warning('Arquivo Indisponível'));
    }

    public function selects(Request $request)
    {
        return Response()->json([
            'units' => Utils::map_select(Data::find(Unit::class, order: ['name'])),
            'status' => Ordinator::list_status(),
        ], 200);
    }
}
