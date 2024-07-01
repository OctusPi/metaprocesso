<?php

namespace App\Http\Controllers;

use App\Models\Comission;
use App\Models\Organ;
use App\Models\Process;
use App\Models\User;
use App\Security\Guardian;
use App\Utils\Notify;
use App\Utils\Utils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Middleware\Data;

class Processes extends Controller
{
    public function __construct()
    {
        parent::__construct(Process::class, User::MOD_PROCCESS);
        Guardian::validateAccess($this->module_id);
    }

    public function save(Request $request)
    {
        return $this->baseSave(Process::class, array_merge($request->all(), [
            'ip' => $request->ip(),
            'author' => $this->user_loged->id
        ]));
    }

    public function list(Request $request)
    {
        return $this->baseList(
            ['organ', 'comission', 'protocol', 'date_ini', 'type', 'modality', 'situation', 'object'],
            ['date_ini'],
            ['organ', 'comission']
        );
    }

    public function update(Request $request)
    {
        $instance = Process::find($request->id);
        if (!$instance) {
            return Response()->json(Notify::warning('Registro não localizado!'), 404);
        }

        if ($verify = $this->verifySituation($instance, Process::S_ANULADO, 'editar')) {
            return $verify;
        }

        return $this->baseUpdate(Process::class, $request->id, $request->all());
    }

    public function delete(Request $request)
    {
        $instance = Process::find($request->id);
        if (!$instance) {
            return Response()->json(Notify::warning('Registro não localizado!'), 404);
        }

        if ($verify = $this->verifySituation($instance, Process::S_ANULADO, 'excluir')) {
            return $verify;
        }

        return parent::delete($request);
    }

    public function selects(Request $request)
    {
        $comissions = $request->key && $request->key == 'organ' ? Utils::map_select(Data::list(Comission::class, [
            [
                'column' => $request->key,
                'operator' => '=',
                'value' => $request->search,
                'mode' => 'AND'
            ]
        ], ['name'])) : Utils::map_select(Data::list(Comission::class));

        return Response()->json([
            'organs' => Utils::map_select(Data::list(Organ::class, order: ['name'])),
            'comissions' => $comissions,
            'types' => Process::list_types(),
            'situations' => Process::list_situations(),
            'modalities' => Process::list_modalitys(),
        ], 200);
    }

    private function verifySituation(Process $process, int $status, string $action): ?JsonResponse
    {
        if ($process->status >= $status) {
            return Response()->json(Notify::warning('Não é possível ' . $action . ' registro!'), 403);
        }

        return null;
    }
}
