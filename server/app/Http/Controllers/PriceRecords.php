<?php

namespace App\Http\Controllers;

use App\Models\Dfd;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use App\Utils\Utils;
use App\Utils\Notify;
use App\Models\DfdItem;
use App\Models\Process;
use App\Models\Comission;
use App\Models\PriceRecord;
use Illuminate\Http\Request;
use App\Http\Middlewares\Data;
use App\Http\Controllers\Controller;

class PriceRecords extends Controller
{
    public function __construct()
    {
        parent::__construct(PriceRecord::class, User::MOD_PRICERECORDS['module']);
    }

    /**
     * Lista processos associados a um Etp.
     *
     * @param Request $request Dados da requisição.
     * @return \Illuminate\Http\JsonResponse Resposta JSON com a lista de processos.
     */
    public function list_processes(Request $request)
    {
        if (!$request->anyFilled('protocol', 'description', 'date_i', 'date_f', 'units')) {
            return response()->json(Notify::warning('Informe pelo menos um campo de busca...'), 500);
        }

        $search = Utils::map_search(['protocol', 'description'], $request->all());
        $betw = $request->date_i && $request->date_f ? ['date_hour_ini' => [$request->date_i, $request->date_f]] : null;
        $objs = Utils::map_search_obj($request->units, 'units', 'id');

        $query = Data::find(new Process(), $search, null, ['organ', 'comission'], $betw, $objs);
        return response()->json($query, 200);
    }

    /**
     * Lista itens de DFD associados a um processo.
     *
     * @param Request $request Dados da requisição.
     * @return \Illuminate\Database\Eloquent\Collection|null Resposta JSON com a lista de itens DFD.
     */
    public function list_dfd_items(Request $request)
    {
        return response()->json(Data::find(new DfdItem(), ['dfd' => $request->id], null, ['item', 'dotation', 'program']));
    }

    /**
     * Lista fornecedores associados a uma coleta de preços.
     *
     * @param Request $request Dados da requisição.
     * @return \Illuminate\Database\Eloquent\Collection|null Resposta JSON com a lista de itens DFD.
     */
    public function list_suppliers(Request $request)
    {
        return response()->json(Data::find(new Supplier(), [
            ['column' => 'name', 'value' => $request->supplier, 'mode' => 'OR'],
            ['column' => 'cnpj', 'value' => $request->supplier, 'mode' => 'OR'],
        ], ['name']));
    }

    /**
     * Retorna seleções de dados para uso em formulários ou filtros.
     *
     * @param Request $request Dados da requisição, incluindo chave para especificar a seleção.
     * @return \Illuminate\Http\JsonResponse Resposta JSON com os dados solicitados.
     */
    public function selects(Request $request)
    {
        return response()->json(array_merge([
            'comissions' => Utils::map_select(Data::find(new Comission(), [], ['name'])),
            'units' => Utils::map_select(Data::find(new Unit(), [], ['name'])),
            'types' => Process::list_types(),
            'process_status' => Process::list_status(),
        ], Dfd::make_details()), 200);
    }
}
