<?php

namespace App\Http\Controllers;

use App\Http\Middlewares\Data;
use App\Models\Comission;
use App\Models\Unit;
use App\Models\User;
use App\Utils\Notify;
use App\Utils\Uploads;
use App\Utils\Utils;
use Illuminate\Http\Request;

class Comissions extends Controller
{
    /**
     * Construtor do controller de Comissões.
     * Define o model associado e o módulo de acesso.
     */
    public function __construct()
    {
        parent::__construct(Comission::class, User::MOD_COMISSIONS['module']);
    }

    /**
     * Lista as comissões baseando-se nos filtros fornecidos.
     */
    public function list(Request $request)
    {
        return $this->base_list(
            $request,
            ['name', 'type', 'description'], // Campos para busca
            ['name'], // Ordenação por nome
            ['organ', 'unit'] // Relações para carregamento adiantado
        );
    }

    /**
     * Salva ou atualiza uma comissão, com suporte a upload de documentos.
     */
    public function save(Request $request)
    {
        // Inicializa o manipulador de upload
        $upload = new Uploads($request, ['document' => ['nullable' => true]]);

        // Remove o documento antigo, se existir
        if ($request->id && $request->hasFile('document')) {
            $instance = $this->model::find($request->id);
            $upload->remove($instance->document);
        }

        // Salva os dados da comissão
        $save = $this->base_save($request, $upload->mergeUploads([]));

        if($save->getStatusCode() == 200) {
            return response()->json(['instance_id' => $this->model->id]);
        }

        return $save;
    }

    /**
     * Faz o download do documento associado à comissão.
     */
    public function download(Request $request)
    {
        $comission = Comission::find($request->id);

        if ($comission && $comission->document) {
            return response()->download(storage_path("uploads/$comission->document"), "$comission->name.pdf");
        }

        return response()->json(Notify::warning('Arquivo Indisponível'));
    }

    /**
     * Retorna os dados de seleção (unidades, tipos, status) necessários para formularios.
     */
    public function selects(Request $request)
    {
        return response()->json([
            'units' => Utils::map_select(Data::find(new Unit(), order: ['name'])),
            'types' => Comission::list_types(),
            'status' => Comission::list_status()
        ], 200);
    }
}
