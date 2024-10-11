<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Utils\Utils;
use App\Utils\Notify;
use App\Models\Catalog;
use App\Http\Middlewares\Data;
use App\Models\Comission;
use App\Models\CatalogItem;
use Illuminate\Http\Request;

class Catalogs extends Controller
{
    /**
     * Construtor do Controller de Catálogos.
     */
    public function __construct()
    {
        parent::__construct(Catalog::class, User::MOD_CATALOGS['module']);
    }

    public function save(Request $request)
    {
        $save = parent::save($request);
        
        if ($save->getStatusCode() == 200) {
            return response()->json(['instance_id' => $this->model->id], 200);
        }

        return $save;
    }


    /**
     * Lista os catálogos de acordo com os parâmetros de busca.
     *
     * @param Request $request Objeto de requisição HTTP.
     * @return \Illuminate\Http\JsonResponse Resposta JSON.
     */
    public function list(Request $request)
    {
        return $this->base_list(
            $request,
            ['comission_id', 'name', 'description'],
            ['name'],
            ['organ', 'comission']
        );
    }

    /**
     * Exporta os dados de um catálogo específico.
     *
     * @param Request $request Objeto de requisição HTTP.
     * @return \Illuminate\Http\JsonResponse Resposta JSON.
     */
    public function export(Request $request)
    {
        $instance = Catalog::with(['organ', 'comission'])->find($request->id);

        if (!$instance) {
            return response()->json(Notify::warning('Catálogo não localizado...'), 404);
        }

        $instanceArray = $instance->toArray();
        $instanceArray['items'] = CatalogItem::where('catalog_id', $request->id)->get();

        return response()->json($instanceArray, 200);
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
            'comissions' => Utils::map_select(Data::find(new Comission(), ['status' => Comission::STATUS_ACTIVE], ['name'])),
            'items_status' => CatalogItem::list_status(),
            'items_categories' => CatalogItem::list_categories(),
            'items_origins' => CatalogItem::list_origins(),
        ], 200);
    }
}
