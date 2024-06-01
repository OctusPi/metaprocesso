<?php

namespace App\Http\Controllers;

use App\Models\ComissionMember;
use App\Models\Unit;
use App\Models\User;
use App\Utils\Utils;
use App\Models\Organ;
use App\Utils\Notify;
use App\Utils\Uploads;
use App\Middleware\Data;
use App\Security\Guardian;
use Illuminate\Http\Request;

class ComissionsMembers extends Controller
{
    public function __construct()
    {
        parent::__construct(ComissionMember::class, User::MOD_MANAGEMENT);
        Guardian::validateAccess($this->module_id);
    }

    public function save(Request $request)
    {
        //upload file
        $upload = new Uploads($request, ['document' => ['nullable' => true]]);
        $values = $upload->mergeUploads($request->all());

        return $this->baseSave(ComissionMember::class, $values);
    }

    public function update(Request $request)
    {
        if (isset($_FILES['document'])) {
            $comission = ComissionMember::findOrFail($request->id);
            $upload = new Uploads($request, ['document' => ['nullable' => true]]);
            $upload->remove($comission->document);
            $values = $upload->mergeUploads($request->all());

            return $this->baseUpdate(ComissionMember::class, $request->id, $values);
        }

        return $this->baseUpdate(ComissionMember::class, $request->id, $request->all());
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
