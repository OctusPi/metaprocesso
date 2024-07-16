<?php

namespace App\Http\Controllers;

use App\Models\Dfd;
use App\Models\Unit;
use App\Utils\Utils;
use App\Models\Organ;
use App\Utils\Notify;
use App\Models\Common;
use App\Models\DfdItem;
use App\Models\Program;
use App\Middleware\Data;
use App\Models\Dotation;
use App\Models\Comission;
use App\Models\Demandant;
use App\Models\Ordinator;
use App\Models\CatalogItem;
use Illuminate\Http\Request;
use App\Models\ComissionMember;
use Illuminate\Support\Facades\Log;

class Dfds extends Controller
{
    public function __construct()
    {
        parent::__construct(Dfd::class, true, Common::MOD_DFDS['module']);
    }

    public function save(Request $request)
    {

        $ip = $request->ip();
        $code = Utils::randCode(6, str_pad($request->unit, 3, '0', STR_PAD_LEFT), date('dmY'));
        $data = array_merge($request->all(), ['ip' => $ip, 'protocol' => $code, 'author' => $request->user()->id]);
        $save = $this->baseSaveInstance($data);

        if (!is_null($save->instance)) {

            $dfd = $save->instance;
            $items = json_decode($request->items, true);
            if (is_array($items)) {
                foreach ($items as $item) {
                    $dfdItem = new DfdItem([
                        'dfd' => $dfd->id,
                        'item' => $item['item']['id'],
                        'quantity' => $item['quantity'],
                        'program' => strlen($item['program'] ?? '') ? $item['program'] : null,
                        'dotation' => strlen($item['dotation'] ?? '') ? $item['dotation'] : null
                    ]);
                    $dfdItem->save();
                }
            }

            return Response()->json(Notify::success('DFD Registrado com Sucesso...'));
        }

        return Response()->json(Notify::warning('Falha ao registrar DFD ' . $save->error), 400);
    }

    public function update(Request $request)
    {
        $dfd = Data::find(Dfd::class, ['id' => $request->id]);
        if(!$dfd || $dfd->status > 3){
            return Response()->json(Notify::warning('Não é possível editar DFDs em uso no processo!'), 403);
        }

        try {
            $req_items = json_decode($request->items, true);
            $req_keys = array_column(array_column($req_items, 'item'), 'id');
            $dfd_items = Data::list(DfdItem::class, ['dfd' => $request->id]);


            //remove items
            foreach ($dfd_items as $dfd_item) {
                if (!in_array($dfd_item['item'], $req_keys)) {
                    DfdItem::destroy($dfd_item['id']);
                }
            }

            // add or edit items
            if (is_array($req_items)) {
                foreach ($req_items as $item) {

                    DfdItem::updateOrCreate(
                        [
                            'dfd' => $request->id,
                            'item' => $item['item']['id']
                        ],
                        [
                            'quantity' => $item['quantity'],
                            'program' => strlen($item['program'] ?? '') ? $item['program'] : null,
                            'dotation' => strlen($item['dotation'] ?? '') ? $item['dotation'] : null
                        ]
                    );
                }
            }

        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return Response()->json(Notify::warning('Falha ao atualizar itens.'), 500);
        }

        return parent::update($request);
    }

    public function delete(Request $request)
    {
        $dfd = Data::find(Dfd::class, ['id' => $request->id]);
        if(!$dfd || $dfd->status > 3){
            return Response()->json(Notify::warning('Não é possível excluir DFDs em uso no processo!'), 403);
        }

        return parent::delete($request);
    }

    public function list(Request $request)
    {
        if(empty($request->all())){
            $betw = ['date_ini' => [date('Y').'-01-01', date('Y-m-d')]];
            return $this->baseList([], ['date_ini'], ['organ', 'unit', 'demandant', 'ordinator'], $betw);
        }

        $betw = $request->date_i && $request->date_f ? ['date_ini' => [$request->date_i, $request->date_f]] : null;
        return $this->baseList(['organ', 'unit', 'protocol', 'description'], ['date_ini'], ['organ', 'unit', 'demandant', 'ordinator'], $betw);
    }

    public function details(Request $request)
    {
        $dfd = Dfd::where('id', $request->id)->first()->toArray();
        $dfd['items'] = DfdItem::where('dfd', $request->id)->with('item')->get();
        if (!$dfd) {
            return Response()->json(Notify::warning('DFD não localizado...'), 404);
        }

        return Response()->json($dfd, 200);
    }

    public function export(Request $request)
    {
        $dfd = Dfd::where('id', $request->id)->with([
            'organ',
            'unit',
            'demandant',
            'ordinator',
            'comission'
        ])->first()->toArray();
        $dfd['items'] = DfdItem::where('dfd', $request->id)->with('item')->get();
        if (!$dfd) {
            return Response()->json(Notify::warning('DFD não localizado...'), 404);
        }

        return Response()->json($dfd, 200);
    }

    public function selects(Request $request)
    {
        //rescue comission_members
        if($request->key == 'comission'){
            $comissions_members = Data::list(ComissionMember::class, ['comission' => $request->search], ['responsibility']);
            return Response()->json($comissions_members);
        }

        $generic = [
            'organs' => Utils::map_select(Data::list(Organ::class, order: ['name'])),
            'prioritys' => Dfd::list_priority(),
            'hirings' => Dfd::list_hirings(),
            'acquisitions' => Dfd::list_acquisitions(),
            'categories' => CatalogItem::list_categories(),
            'responsibilitys' => ComissionMember::list_responsabilities(),
            'status' => Dfd::list_status()
        ];

        // rescue organs
        if(is_null($request->key)){
            return Response()->json($generic);
        } 

        // rescues other seclects by organ or unit
        if ($request->key == 'organ' || $request->key == 'unit') {
            
            $units = $request->key == 'organ' 
            ? Utils::map_select(Data::list(Unit::class, [$request->key => $request->search], ['name'])) 
            : Utils::map_select(Data::list(Unit::class));

            $ordinators = Utils::map_select(Data::list(Ordinator::class, [$request->key => $request->search], ['name']));
            $demandants = Utils::map_select(Data::list(Demandant::class, [$request->key => $request->search], ['name']));
            $comissions = Utils::map_select(Data::list(Comission::class, [$request->key => $request->search], ['name']));
            $programs = Utils::map_select(Data::list(Program::class, [$request->key => $request->search], ['name']));
            $dotations = Utils::map_select(Data::list(Dotation::class, [$request->key => $request->search], ['name']));

            $especializer = array_merge($generic, [
                'units' => $units,
                'ordinators' => $ordinators,
                'demandants' => $demandants,
                'comissions' => $comissions,
                'programs' => $programs,
                'dotations' => $dotations
            ]);

            return Response()->json($especializer);
        }

        return Response()->json(Notify::warning("Selects não localizados..."), 404);

    }

    public function items(Request $request)
    {
        return (new CatalogItems())->list($request);
    }
}
