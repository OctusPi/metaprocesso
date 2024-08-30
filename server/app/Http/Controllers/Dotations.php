<?php

namespace App\Http\Controllers;

use App\Http\Middlewares\Data;
use App\Models\Dotation;
use App\Models\Program;
use App\Models\Unit;
use App\Models\User;
use App\Utils\Utils;
use Illuminate\Http\Request;

class Dotations extends Controller
{
    /**
     * Construtor para o controller de Dotações.
     */
    public function __construct()
    {
        // Inicializa o controller especificando o modelo e o módulo de acesso
        parent::__construct(Dotation::class, User::MOD_DOTATIONS['module']);
    }

    /**
     * Lista todas as Dotações com base em parâmetros de filtragem.
     *
     * @param Request $request Dados da requisição incluindo filtros.
     * @return \Illuminate\Http\JsonResponse Resposta JSON com a lista de Dotações.
     */
    public function list(Request $request)
    {
        // Retorna a lista base usando critérios de filtragem e ordenação
        return $this->base_list(
            $request,
            ['unit', 'name', 'law'], // Campos para filtro
            ['name'], // Campo para ordenação
            ['organ', 'unit'] // Relações para carregamento adiantado (eager loading)
        );
    }

    /**
     * Fornece seleções de dados para uso em formulários ou interfaces.
     *
     * @param Request $request Dados da requisição para especificar seleções.
     * @return \Illuminate\Http\JsonResponse Resposta JSON com dados de seleção.
     */
    public function selects(Request $request)
    {
        // Recupera unidades e status para fornecer dados de seleção
        $units = Utils::map_select(Data::find(new Unit(), order:['name']));
        $status = Program::list_status();

        // Retorna os dados formatados em JSON
        return response()->json([
            'units' => $units,
            'status' => $status
        ], 200);
    }
}
