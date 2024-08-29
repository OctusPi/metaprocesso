<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Organ;
use Illuminate\Http\Request;

class Organs extends Controller
{
    /**
     * Construtor do controller Organs.
     * Inicializa o controller com o modelo Organ e o módulo específico.
     */
    public function __construct()
    {
        parent::__construct(Organ::class, User::MOD_ORGANS['module']);
    }

    /**
     * Lista os órgãos com base nos parâmetros de filtragem fornecidos.
     *
     * @param Request $request Dados da requisição para filtragem.
     * @return \Illuminate\Http\JsonResponse Resposta JSON com a lista de órgãos.
     */
    public function list(Request $request)
    {
        return $this->base_list(
            $request,
            ['name', 'cnpj', 'postalcity'], // Campos para filtro
            ['name'] // Campo para ordenação
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
        return response()->json([
            'status' => Organ::list_status()
        ], 200);
    }
}
