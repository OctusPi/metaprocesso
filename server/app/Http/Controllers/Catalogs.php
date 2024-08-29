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
            ['comission', 'name', 'description'],
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
        $instanceArray['items'] = CatalogItem::where('catalog', $request->id)->get();

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
        // Mapeia as comissões de acordo com o parâmetro de busca, se fornecido
        $comissions = $request->key
            ? Utils::map_select(Data::find(new Comission(), [
                [
                    'column' => $request->key,
                    'operator' => '=',
                    'value' => $request->search,
                    'mode' => 'AND'
                ]
            ], ['name']))
            : Utils::map_select(Data::find(new Comission()));

        return response()->json([
            'comissions' => $comissions,
            'items_status' => CatalogItem::list_status(),
            'items_categories' => CatalogItem::list_categories(),
            'items_origins' => CatalogItem::list_origins(),
        ], 200);
    }
}
