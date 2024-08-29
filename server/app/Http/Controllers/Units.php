<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;

class Units extends Controller
{
    /**
     * Construtor para o controller de Unidades.
     */
    public function __construct()
    {
        // Inicializa o controller com o modelo Unit e define o módulo de acesso.
        parent::__construct(Unit::class, User::MOD_UNITS['module']);
    }

    /**
     * Lista todas as unidades com base nos critérios de filtragem fornecidos.
     *
     * @param Request $request Dados da requisição para filtragem e ordenação.
     * @return \Illuminate\Http\JsonResponse Resposta JSON com a lista de unidades.
     */
    public function list(Request $request)
    {
        // Retorna a lista base usando critérios de filtragem e ordenação
        return $this->base_list(
            $request,
            ['name', 'cnpj', 'address'], // Campos para filtragem
            ['name'], // Campo para ordenação
            ['organ'] // Relações para carregamento adiantado
        );
    }

    /**
     * Fornece dados de seleção para uso em formulários ou interfaces.
     *
     * @param Request $request Dados da requisição.
     * @return \Illuminate\Http\JsonResponse Resposta JSON com os dados de seleção.
     */
    public function selects(Request $request)
    {
        // Recupera e retorna os status das unidades para seleção
        return response()->json([
            'status' => Unit::list_status()
        ], 200);
    }
}
