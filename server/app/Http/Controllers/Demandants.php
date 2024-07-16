<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Utils\Utils;
use App\Models\Organ;
use App\Utils\Notify;
use App\Models\Common;
use App\Models\Sector;
use App\Utils\Uploads;
use App\Middleware\Data;
use App\Models\Demandant;
use Illuminate\Http\Request;

class Demandants extends Controller
{
    public function __construct()
    {
        parent::__construct(Demandant::class, true, Common::MOD_DEMANDANTS['module']);
    }

    public function save(Request $request)
    {
        //upload file
        $upload = new Uploads($request, ['document' => ['nullable' => true]]);
        $values = $upload->mergeUploads($request->all());

        return $this->baseSave(Demandant::class, $values);
    }

    public function update(Request $request)
    {
        if(isset($_FILES['document'])){
            $ordinator = Demandant::findOrFail($request->id);
            $upload = new Uploads($request, ['document' => ['nullable' => true]]);
            $upload->remove($ordinator->document);
            $values = $upload->mergeUploads($request->all());

            return $this->baseUpdate(Demandant::class, $request->id, $values);
        }

        return $this->baseUpdate(Demandant::class, $request->id, $request->all());
    }

    public function list(Request $request)
    {
        return $this->baseList(['organ', 'unit', 'name'], ['name'], ['organ', 'unit']);
    }

    public function download(Request $request)
    {
        $ordinator = Demandant::findOrFail($request->id);
        if($ordinator && $ordinator->document){
            return response()->download(storage_path('uploads'.'/'.$ordinator->document), $ordinator->name.'.pdf');
        }

        return response()->json(Notify::warning('Arquivo Indisponível'));
    }

    public function selects(Request $request)
    {
        $units = $request->key && $request->key == 'organ' ? Utils::map_select(Data::list(Unit::class, [
            [
                'column'   => $request->key,
                'operator' => '=',
                'value'    => $request->search,
                'mode'     => 'AND'
            ]
            ], ['name'])) : Utils::map_select(Data::list(Unit::class));

        $sectors = $request->key && $request->key == 'unit' ? Utils::map_select(Data::list(Sector::class, [
            [
                'column'   => $request->key,
                'operator' => '=',
                'value'    => $request->search,
                'mode'     => 'AND'
            ]
            ], ['name'])) : Utils::map_select(Data::list(Sector::class));

        return Response()->json([
            'organs'  => Utils::map_select(Data::list(Organ::class, order:['name'])),
            'units'   => $units,
            'sectors' => $sectors,
            'status'  => Demandant::list_status()
        ], 200);
    }
}
