<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Supplier;
use App\Utils\Notify;
use Illuminate\Http\Request;
use Validator;

class Suppliers extends Controller
{
    /**
     * Construtor da classe Suppliers.
     * Inicializa o controller com o modelo Supplier e o módulo específico.
     */
    public function __construct()
    {
        parent::__construct(Supplier::class, User::MOD_SUPPLIERS['module']);
    }

    /**
     * Lista todos os fornecedores com base nos critérios de filtragem.
     *
     * @param Request $request Dados da requisição para filtragem.
     * @return \Illuminate\Http\JsonResponse Resposta JSON com a lista de fornecedores.
     */
    public function list(Request $request)
    {
        // Retorna a lista base usando critérios de filtragem e ordenação
        return $this->base_list(
            $request,
            ['name', 'cnpj', 'address', 'modality', 'size'],  // Campos para filtragem
            ['name']  // Campo para ordenação
        );
    }

    /**
     * Fornece dados de seleção para uso em formulários ou interfaces.
     *
     * @param Request $request Dados da requisição.
     * @return \Illuminate\Http\JsonResponse Resposta JSON com dados de seleção.
     */
    public function selects(Request $request)
    {
        // Recupera e retorna dados de modalidades e tamanhos de fornecedores
        return response()->json([
            'modalities' => Supplier::list_modalitys(),
            'sizes' => Supplier::list_sizes()
        ]);
    }

    public function send_form(Request $request)
    {
        $data = ['emails' => json_decode($request->emails)];

        $validation = Validator::make($data, [
            'emails' => 'required|array',
            'emails.*' => 'required|email'
        ]);

        if ($validation->fails()) {
            return response()->json(Notify::warning('Um ou mais emails inválidos'), 400);
        }

        $emails = collect($data['emails']);
    }
}
