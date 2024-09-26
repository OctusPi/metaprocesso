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
        $this->model = $request->id ? Data::findOne(new Dfd(), ['id' => $request->id]) : $this->model;

        if ($this->model && $this->model->status >= Dfd::STATUS_BLOQUEADO) {
            return response()->json(Notify::warning('Não é possível editar DFDs aprovisionados pelo Processo!'), 403);
        }

        $protocol = $request->id ? $request->protocol : Utils::randCode(6, str_pad($request->unit, 3, '0', STR_PAD_LEFT), date('dmY'));

        $data = [
            'ip' => $request->ip(),
            'protocol' => $protocol,
            'author' => $request->user()->id,
            'comission_members' => ComissionMember::where('comission', $request->comission)->get()->toArray()
        ];

        $save = $this->base_save($request, $data);
        if($save->status() == 200){

            $this->manageItems($request);
        }

        return $save;
    }

    /**
     * Mostra detalhes de um DFD específico.
     *
     * @param Request $request Dados da requisição incluindo o ID do DFD.
     * @return \Illuminate\Http\JsonResponse Resposta JSON com detalhes do DFD.
     */
    public function details(Request $request)
    {
        $dfd = $this->base_details($request);

        if($dfd->status() == 200){
            $items = Data::find(new DfdItem(), ['dfd' => $request->id], with:['item']) ?? [];
            $group = array_merge($dfd->getData(true) ?? [], ['items' => $items]);
            return response()->json($group);
        }

        return response()->json(Notify::warning("DFD não localizado..."), $dfd->status());
    }

    /**
     * Recupera os dados do DFD para exportar.
     *
     * @param Request $request Dados da requisição incluindo o ID do DFD.
     * @return \Illuminate\Http\JsonResponse Resposta JSON com detalhes do DFD.
     */
    public function export(Request $request)
    {
        $dfd = $this->base_details($request, ['unit', 'ordinator', 'demandant', 'comission']);

        if($dfd->status() == 200){
            $data  = $dfd->getData(true) ?? [];
            $items = Data::find(new DfdItem(), ['dfd' => $request->id], with:['item']) ?? [];
            $programs  = Data::find(new Program(), ['unit' => $data['unit']['id']], ['name']);
            $dotations = Data::find(new Dotation(), ['unit' => $data['unit']['id']], ['name']);
            $group = array_merge($data, ['items' => $items, 'programs' => $programs, 'dotations' => $dotations]);
            return response()->json($group);
        }

        return response()->json(Notify::warning("DFD não localizado..."), $dfd->status());
    }

    public function delete(Request $request)
    {

        $this->check_auth($request);

        if (!password_verify($request->password, $request->user()->getAttribute('password'))) {
            return response()->json(Notify::warning('Senha de confirmação incorreta!'), 401);
        }

        $this->model = Data::findOne(new Dfd(), ['id' => $request->id]);

        if ($this->model && $this->model->status >= Dfd::STATUS_BLOQUEADO) {
            return response()->json(Notify::warning('Não é possível APAGAR DFDs bloqueado pelo Processo!'), 403);
        }

        DfdItem::where('dfd', $request->id)->delete();

        return $this->base_delete($request);

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
            'items_types'   => CatalogItem::list_types(),
            'responsibilitys' => ComissionMember::list_responsabilities(),
            'status'        => Dfd::list_status()
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
     */
    private function manageItems(Request $request)
    {
        if (!$this->model->id) return;

        $currentItems = Data::find(new DfdItem(), ['dfd' => $this->model->id])->pluck('id');
        $newItems = collect(json_decode($request->items, true))->keyBy('id');

        foreach ($currentItems as $itemId) {
            if (!$newItems->has($itemId)) {
                DfdItem::destroy($itemId);
            }
        }

        foreach ($newItems as $itemData) {

            DfdItem::updateOrCreate(
                [
                    'id'       => is_numeric($itemData['id']) && !$request->clone ? $itemData['id'] : null,
                    'dfd'      => $this->model->id,
                    'item'     => $itemData['item']['id'],
                    'quantity' => $itemData['quantity'],
                    'program'  => $itemData['program'] ?? null,
                    'dotation' => $itemData['dotation'] ?? null
                ]
            );
        }
    }
}
