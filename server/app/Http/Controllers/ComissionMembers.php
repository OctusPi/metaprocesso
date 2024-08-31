<?php

namespace App\Http\Controllers;

use App\Http\Middlewares\Data;
use App\Models\Comission;
use App\Models\User;
use App\Utils\Notify;
use App\Utils\Uploads;
use Illuminate\Http\Request;
use App\Models\ComissionMember;

class ComissionMembers extends Controller
{
    /**
     * Construtor do Controller de Membros de Comissão.
     */
    public function __construct()
    {
        parent::__construct(ComissionMember::class, User::MOD_COMISSIONS['module']);
    }

    /**
     * Salva um membro de comissão, incluindo upload de documentos.
     *
     * @param Request $request Objeto de requisição HTTP.
     * @return \Illuminate\Http\JsonResponse Resposta JSON.
     */
    public function save(Request $request)
    {
        // Verifica se a comissão existe
        $comission = Data::findOne(new Comission(), ['id' => $request->comission]);
        if (!$comission) {
            return response()->json(Notify::warning('Comissão inexistente'), 404);
        }

        // Gerencia o upload de documentos
        $upload = new Uploads($request, ['document' => ['nullable' => true]]);

        if ($request->id && $request->hasFile('document')) {
            $instance = $this->model::find($request->id);
            $upload->remove($instance->document);
        }

        // Salva o membro de comissão com os dados atualizados
        return $this->base_save(
            $request,
            $upload->mergeUploads([
                'organ' => $comission->organ,
                'unit' => $comission->unit,
                'comission' => $comission->id
            ])
        );
    }

    /**
     * Lista membros da comissão conforme parâmetros de busca.
     *
     * @param Request $request Objeto de requisição HTTP.
     * @return \Illuminate\Http\JsonResponse Resposta JSON.
     */
    public function list(Request $request)
    {
        return $this->base_list(
            $request,
            ['comission', 'status', 'responsibility', 'name'],
            ['name']
        );
    }

    public function comission(Request $request)
    {
        $request->id = $request->comission;
        return (new Comissions())->base_details($request);
    }

    /**
     * Realiza o download do documento de um membro da comissão.
     *
     * @param Request $request Objeto de requisição HTTP.
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse Resposta de download ou JSON.
     */
    public function download(Request $request)
    {
        $member = ComissionMember::find($request->id);

        if ($member && $member->document) {
            return response()->download(storage_path("uploads/$member->document"), "$member->name.pdf");
        }

        return response()->json(Notify::warning('Arquivo Indisponível'));
    }

    /**
     * Retorna dados de seleção para uso em formulários ou filtros.
     *
     * @param Request $request Objeto de requisição HTTP.
     * @return \Illuminate\Http\JsonResponse Resposta JSON.
     */
    public function selects(Request $request)
    {
        return response()->json([
            'responsibilities' => ComissionMember::list_responsabilities(),
            'status' => ComissionMember::list_status(),
        ], 200);
    }
}
