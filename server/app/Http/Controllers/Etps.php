<?php

namespace App\Http\Controllers;

use App\Models\Comission;
use App\Models\Dfd;
use App\Models\Organ;
use App\Models\Unit;
use App\Models\User;
use App\Utils\Utils;
use App\Utils\Notify;
use App\Utils\Uploads;
use App\Middleware\Data;
use App\Models\Etp;
use App\Security\Guardian;
use Illuminate\Http\Request;

class Etps extends Controller
{
    public function __construct()
    {
        parent::__construct(Etp::class, User::MOD_DFDS);
        Guardian::validateAccess($this->module_id);
    }

    public function save(Request $request)
    {
        $upload = new Uploads($request, [
            'contract_calculus_memories_file' => ['nullable' => false],
            'contract_expected_price_file' => ['nullable' => false],
        ]);

        return $this->baseSave(Etp::class, $upload->mergeUploads($request->all()));
    }

    public function update(Request $request)
    {
        $values = $request->all();

        if ($request->file('contract_calculus_memories_file') || $request->file('contract_expected_price_file')) {
            $etp = Etp::findOrFail($request->id);
            $upload = new Uploads($request, [
                'contract_calculus_memories_file' => ['nullable' => true],
                'contract_expected_price_file' => ['nullable' => true],
            ]);
            if ($request->file('contract_calculus_memories_file')) {
                $upload->remove($etp->contract_calculus_memories_file);
            }
            if ($request->file('contract_expected_price_file')) {
                $upload->remove($etp->contract_expected_price_file);
            }

            $values = $upload->mergeUploads($values);
        }

        return $this->baseUpdate(Etp::class, $request->id, $values);
    }

    public function list(Request $request)
    {
        return $this->baseList(['number', 'dfds', 'necessity'], ['number']);
    }

    public function details(Request $request)
    {
        return $this->baseDetails(Etp::class, $request->id);
    }

    public function download(Request $request)
    {
        $etp = Etp::findOrFail($request->id);
        if ($request->contract_calculus_memories_file && $etp->contract_calculus_memories_file) {
            return response()->download(storage_path('uploads' . '/' . $etp->contract_calculus_memories_file), $etp->number . '.pdf');
        }

        if ($request->contract_expected_price_file && $etp->contract_expected_price_file) {
            return response()->download(storage_path('uploads' . '/' . $etp->contract_expected_price_file), $etp->number . '.pdf');
        }

        return response()->json(Notify::warning('Arquivo Indisponível'));
    }

    public function selects(Request $request)
    {
        $comission = $request->key && $request->key == 'organ' ? Utils::map_select(Data::list(Comission::class, [
            [
                'column' => $request->key,
                'operator' => '=',
                'value' => $request->search,
                'mode' => 'AND'
            ]
        ], ['name'])) : Utils::map_select(Data::list(Comission::class));

        $units = $request->key && $request->key == 'organ' ? Utils::map_select(Data::list(Unit::class, [
            [
                'column' => $request->key,
                'operator' => '=',
                'value' => $request->search,
                'mode' => 'AND'
            ]
        ], ['name'])) : Utils::map_select(Data::list(Unit::class));
        
        return Response()->json([
            'dfds' => Utils::map_select(Data::list(Dfd::class, order: ['code'])),
            'organs' => Utils::map_select(Data::list(Organ::class, order: ['name'])),
            'status' => Etp::list_status(),
            'comissions' => $comission,
            'units' => $units,
        ], 200);
    }
}
