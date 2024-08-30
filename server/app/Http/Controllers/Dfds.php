<?php

namespace App\Http\Controllers;

use App\Http\Middlewares\Data;
use App\Models\{CatalogItem, Comission, ComissionMember, Demandant, Dfd, DfdItem, Dotation, Ordinator, Program, Unit, User};
use App\Utils\Notify;
use App\Utils\Utils;
use Illuminate\Http\Request;

class Dfds extends Controller
{
    /**
     * Constrói o controller DFDs.
     */
    public function __construct()
    {
        parent::__construct(Dfd::class, User::MOD_DFDS['module']);
    }

    /**
     * Lista os DFDs com base em critérios específicos de filtragem.
     *
     * @param Request $request Dados da requisição incluindo filtros.
     * @return \Illuminate\Http\JsonResponse Resposta JSON com a lista de DFDs.
     */
    public function list(Request $request)
    {
        $date_between = $request->has(['date_i', 'date_f']) ?
                        ['date_ini' => [$request->date_i, $request->date_f]] :
                        ['date_ini' => [date('Y') . '-01-01', date('Y-m-d')]];

        return $this->base_list(
            $request,
            ['organ', 'unit', 'protocol', 'description'],
            ['date_ini'],
            ['organ', 'unit', 'demandant', 'ordinator', 'comission'],
            $date_between
        );
    }

    /**
     * Salva ou atualiza um DFD.
     *
     * @param Request $request Dados da requisição para salvar um DFD.
     * @return \Illuminate\Http\JsonResponse Resposta JSON do resultado da operação.
     */
    public function save(Request $request)
    {
        $dfd = $request->id ? Data::findOne(new Dfd(), ['id' => $request->id]) : null;

        if ($dfd && $dfd->status >= Dfd::STATUS_BLOQUEADO) {
            return response()->json(Notify::warning('Não é possível editar DFDs bloqueado pelo Processo!'), 403);
        }

        $protocol = $request->id ? $request->protocol : Utils::randCode(6, str_pad($request->unit, 3, '0', STR_PAD_LEFT), date('dmY'));

        $data = [
            'ip' => $request->ip(),
            'protocol' => $protocol,
            'author' => $request->user()->id
        ];

        $response = $this->base_save($request, $data);
        $this->manageItems($request, $response->instance ?? null);

        return $response;
    }

    /**
     * Mostra detalhes de um DFD específico.
     *
     * @param Request $request Dados da requisição incluindo o ID do DFD.
     * @return \Illuminate\Http\JsonResponse Resposta JSON com detalhes do DFD.
     */
    public function details(Request $request)
    {
        $instance = Dfd::with(['items.item'])->find($request->id);

        if (!$instance) {
            return response()->json(Notify::warning('DFD não localizado...'), 404);
        }

        return response()->json($instance, 200);
    }

    /**
     * Retorna seleções de dados para uso em formulários ou filtros.
     *
     * @param Request $request Dados da requisição, incluindo chave para especificar a seleção.
     * @return \Illuminate\Http\JsonResponse Resposta JSON com os dados solicitados.
     */
    public function selects(Request $request)
    {
        $selections = [
            'units'         => Utils::map_select(Data::find(new Unit(), [], ['name'])),
            'comissions'    => Utils::map_select(Data::find(new Comission(), [], ['name'])),
            'acquisitions'  => Dfd::list_acquisitions(),
            'prioritys'     => Dfd::list_priority(),
            'hirings'       => Dfd::list_hirings(),
            'categories'    => CatalogItem::list_categories(),
            'items_types'   => CatalogItem::list_types()
        ];

        if($request->key == 'filter'){
            [$unit_id, $comission_id] = array_pad(explode(',', $request->search), 2, null);

            $selections['ordinators'] = Data::find(new Ordinator(), ['unit' => $unit_id], ['name']);
            $selections['demandants'] = Data::find(new Demandant(), ['unit' => $unit_id], ['name']);
            $selections['programs'] = Data::find(new Program(), ['unit' => $unit_id], ['name']);
            $selections['dotations'] = Data::find(new Dotation(), ['unit' => $unit_id], ['name']);

            $selections['comission_members'] = $comission_id
            ? Data::find(new ComissionMember(), ['comission' => $comission_id])
            : null;
        }

        return response()->json($selections, 200);
    }

    public function items(Request $request)
    {
        return (new CatalogItems())->list($request);
    }

    /**
     * Gerencia a adição ou remoção de itens de um DFD.
     *
     * @param Request $request Dados da requisição.
     * @param Dfd|null $dfd Instância do DFD para adicionar ou remover itens.
     */
    private function manageItems(Request $request, ?Dfd $dfd)
    {
        if (!$dfd) return;

        $currentItems = $dfd->items()->pluck('id')->toArray();
        $newItems = collect(json_decode($request->items, true))->keyBy('id');

        foreach ($currentItems as $itemId) {
            if (!$newItems->has($itemId)) {
                DfdItem::destroy($itemId);
            }
        }

        foreach ($newItems as $itemData) {
            DfdItem::updateOrCreate(
                ['dfd_id' => $dfd->id, 'item_id' => $itemData['item']['id']],
                ['quantity' => $itemData['quantity']]
            );
        }
    }
}
