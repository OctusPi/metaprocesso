<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Utils\Utils;
use App\Models\Organ;
use App\Utils\Notify;
use App\Models\Common;
use App\Utils\Uploads;
use App\Middleware\Data;
use App\Models\Ordinator;
use Illuminate\Http\Request;

class Ordinators extends Controller
{
    public function __construct()
    {
        parent::__construct(Ordinator::class, true, Common::MOD_ORDINATORS['module']);
    }

    public function save(Request $request)
    {
        //upload file
        $upload = new Uploads($request, ['document' => ['nullable' => true]]);
        $values = $upload->mergeUploads($request->all());

        return $this->baseSave(Ordinator::class, $values);
    }

    public function update(Request $request)
    {
        if(isset($_FILES['document'])){
            $ordinator = Ordinator::findOrFail($request->id);
            $upload = new Uploads($request, ['document' => ['nullable' => true]]);
            $upload->remove($ordinator->document);
            $values = $upload->mergeUploads($request->all());

            return $this->baseUpdate(Ordinator::class, $request->id, $values);
        }

        return $this->baseUpdate(Ordinator::class, $request->id, $request->all());
    }

    public function list(Request $request)
    {
        return $this->baseList(['organ', 'unit', 'name'], ['name'], ['organ', 'unit']);
    }

    public function download(Request $request)
    {
        $ordinator = Ordinator::findOrFail($request->id);
        if($ordinator && $ordinator->document){
            return response()->download(storage_path('uploads'.'/'.$ordinator->document), $ordinator->name.'.pdf');
        }

        return response()->json(Notify::warning('Arquivo Indisponível'));
    }

    public function selects(Request $request)
    {
        $units = $request->key ? Utils::map_select(Data::list(Unit::class, [
            [
                'column'   => $request->key,
                'operator' => '=',
                'value'    => $request->search,
                'mode'     => 'AND'
            ]
            ], ['name'])) : Utils::map_select(Data::list(Unit::class));

        return Response()->json([
            'organs' => Utils::map_select(Data::list(Organ::class, order:['name'])),
            'units'  => $units,
            'status' => Ordinator::list_status()
        ], 200);
    }
}
