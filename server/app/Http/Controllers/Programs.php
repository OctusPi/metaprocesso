<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\User;
use App\Utils\Utils;
use App\Models\Program;
use App\Http\Middlewares\Data;
use Illuminate\Http\Request;

class Programs extends Controller
{
    /**
     * Inicializa o controller de Programas com o modelo e módulo específico.
     */
    public function __construct()
    {
        parent::__construct(Program::class, User::MOD_PROGRAMS['module']);
    }

    /**
     * Lista os programas com base em critérios de filtragem especificados.
     *
     * @param Request $request Dados da requisição para filtragem e ordenação.
     * @return \Illuminate\Http\JsonResponse Resposta JSON com a lista de programas.
     */
    public function list(Request $request)
    {
        return $this->base_list(
            $request,
            ['unit_id', 'name', 'law'],  // Campos para filtragem
            ['name'],  // Campo para ordenação
            ['organ', 'unit']  // Relações para carregamento adiantado
        );
    }

    /**
     * Fornece seleções de dados para uso em formulários ou interfaces.
     *
     * @param Request $request Dados da requisição.
     * @return \Illuminate\Http\JsonResponse Resposta JSON com os dados de seleção.
     */
    public function selects(Request $request)
    {
        // Recupera e mapeia unidades e status para seleção
        $units = Utils::map_select(Data::find(new Unit(), [], ['name']));
        $status = Program::list_status();

        // Retorna a resposta JSON com as seleções formatadas
        return response()->json([
            'units' => $units,
            'status' => $status
        ], 200);
    }
}
