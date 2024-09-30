<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\User;
use App\Utils\Utils;
use App\Utils\Notify;
use App\Utils\Uploads;
use App\Models\Ordinator;
use Illuminate\Http\Request;
use App\Http\Middlewares\Data;

class Ordinators extends Controller
{
    /**
     * Construtor do Controller de Ordenadores.
     */
    public function __construct()
    {
        // Inicializa o controller com o modelo Ordinator e o módulo de acesso.
        parent::__construct(Ordinator::class, User::MOD_ORDINATORS['module']);
    }

    /**
     * Exibe uma lista de ordenadores.
     *
     * @param Request $request Dados da requisição para filtragem.
     * @return \Illuminate\Http\JsonResponse Resposta JSON com a lista de ordenadores.
     */
    public function list(Request $request)
    {
        return $this->base_list(
            $request,
            ['unit_id', 'name', 'status'],
            ['name'],
            ['organ', 'unit']
        );
    }

    /**
     * Salva ou atualiza um ordenador, tratando também o upload de documentos.
     *
     * @param Request $request Dados da requisição incluindo o arquivo de documento, se presente.
     * @return \Illuminate\Http\JsonResponse Resposta JSON do resultado da operação de salvamento.
     */
    public function save(Request $request)
    {
        $upload = new Uploads($request, ['document' => ['nullable' => true]]);

        if ($request->id && $request->hasFile('document')) {
            $instance = $this->model::find($request->id);
            $upload->remove($instance->document);
        }

        return $this->base_save($request, $upload->mergeUploads([]));
    }

    /**
     * Permite o download do documento associado ao ordenador.
     *
     * @param Request $request Dados da requisição que incluem o ID do ordenador.
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse Resposta de download ou erro.
     */
    public function download(Request $request)
    {
        $ordinator = Ordinator::find($request->id);

        if ($ordinator && $ordinator->document) {
            return response()->download(storage_path("uploads/{$ordinator->document}"), "{$ordinator->name}.pdf");
        }

        return response()->json(Notify::warning('Arquivo Indisponível'));
    }

    /**
     * Fornece seleções de dados para uso em formulários ou interfaces.
     *
     * @param Request $request Dados da requisição.
     * @return \Illuminate\Http\JsonResponse Resposta JSON com dados de seleção.
     */
    public function selects(Request $request)
    {
        return response()->json([
            'units' => Utils::map_select(Data::find(new Unit(), [], ['name'])),
            'status' => Ordinator::list_status(),
        ], 200);
    }
}
