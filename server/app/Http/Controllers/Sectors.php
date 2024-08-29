<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\User;
use App\Utils\Utils;
use App\Models\Sector;
use Illuminate\Http\Request;
use App\Http\Middlewares\Data;

class Sectors extends Controller
{
    /**
     * Construtor que inicializa o controller com o modelo de Setor e o módulo específico.
     */
    public function __construct()
    {
        parent::__construct(Sector::class, User::MOD_SECTORS['module']);
    }

    /**
     * Lista os setores com base nos critérios de filtragem.
     *
     * @param Request $request Dados da requisição para filtragem.
     * @return \Illuminate\Http\JsonResponse Resposta JSON com a lista de setores.
     */
    public function list(Request $request)
    {
        return $this->base_list(
            $request,
            ['unit', 'name', 'description'],  // Campos para filtragem
            ['name'],  // Campo para ordenação
            ['organ', 'unit']  // Relações para carregamento adiantado
        );
    }

    /**
     * Fornece dados de seleção para uso em formulários ou interfaces.
     *
     * @param Request $request Dados da requisição.
     * @return \Illuminate\Http\JsonResponse Resposta JSON com dados de seleção para unidades.
     */
    public function selects(Request $request)
    {
        // Recupera e formata os dados de seleção das unidades
        $units = Utils::map_select(Data::find(new Unit(), [], ['name']));

        // Retorna os dados formatados em JSON
        return response()->json([
            'units' => $units
        ], 200);
    }
}
