<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\User;
use App\Utils\Utils;
use App\Models\Organ;
use App\Models\Sector;
use App\Utils\Uploads;
use App\Middleware\Data;
use App\Models\Ordinator;
use App\Security\Guardian;
use Illuminate\Http\Request;

class Ordinators extends Controller
{
    public function __construct()
    {
        parent::__construct(User::MOD_MANAGEMENT);
        Guardian::validateAccess($this->module_id);
    }

    public function save(Request $request)
    {
        //upload file
        $upload = new Uploads($request, ['document']);
        $values = $upload->mergeUploads($request->all());

        return $this->baseSave(Ordinator::class, $values);
    }

    public function update(Request $request)
    {
        if(isset($_FILES['document'])){
            $ordinator = Ordinator::findOrFail($request->id);
            $upload = new Uploads($request, ['document']);
            $upload->remove($ordinator->document);
            $values = $upload->mergeUploads($request->all());

            return $this->baseUpdate(Ordinator::class, $request->id, $values);
        }

        return $this->baseUpdate(Ordinator::class, $request->id, $request->all());
    }

    public function delete(Request $request)
    {
        return $this->baseDelete(Ordinator::class, $request->id, $request->password);
    }

    public function list(Request $request)
    {
        $search = ['organ_id', 'unit_id', 'name'];
        return $this->baseList(Ordinator::class, $search, $request->all());
    }

    public function details(Request $request)
    {
        return $this->baseDetails(Ordinator::class, $request->id);
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
