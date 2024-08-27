<?php

namespace App\Http\Controllers;

use App\Http\Middlewares\Data;
use App\Models\Catalog;
use App\Models\CatalogItem;
use App\Models\Comission;
use App\Models\ComissionMember;
use App\Models\Demandant;
use App\Models\Dfd;
use App\Models\DfdItem;
use App\Models\Dotation;
use App\Models\Ordinator;
use App\Models\Program;
use App\Models\Unit;
use App\Models\User;
use App\Utils\Notify;
use App\Utils\Utils;
use Illuminate\Http\Request;

class Dfds extends Controller
{
    public function __construct()
    {
        parent::__construct(Dfd::class, User::MOD_DFDS['module']);
    }

    public function list(Request $request)
    {
        $date_between = ['date_ini' => [date('Y') . '-01-01', date('Y-m-d')]];

        if ($request->has('date_i', 'date_f')) {
            $date_between = ['date_ini' => [$request->date_i, $request->date_f]];
        }

        return $this->base_list(
            $request,
            ['organ', 'unit', 'protocol', 'description'],
            ['date_ini'],
            ['organ', 'unit', 'demandant', 'ordinator', 'comission'],
            $date_between,
            organ: true
        );
    }

    public function save(Request $request)
    {
        if ($request->id) {
            $dfd = Data::findOne(Dfd::class, ['id' => $request->id]);
            if (!$dfd || $dfd->status >= Dfd::STATUS_BLOQUEADO) {
                return Response()->json(Notify::warning('Não é possível editar DFDs em uso no processo!'), 403);
            }
        }

        $ip = $request->ip();

        $code = $request->id ?
            $request->protocol
            : Utils::randCode(6, str_pad($request->unit, 3, '0', STR_PAD_LEFT), date('dmY'));

        $data = [
            'ip' => $ip,
            'protocol' => $code,
            'author' => $request->user()->id
        ];

        $save = $this->getter_save($request, $data);

        $this->removeItems($request);

        if (!is_null($save->instance)) {
            $items = json_decode($request->items, true);
            if (is_array($items)) {
                foreach ($items as $item) {
                    $dfdItem = new DfdItem([
                        'dfd' => $save->instance->id,
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

        return Response()->json(Notify::warning("Falha ao registrar DFD $save->error"), 400);
    }

    public function details(Request $request)
    {
        $instance = Dfd::find($request->id)->toArray();

        if (!$instance) {
            return Response()->json(Notify::warning('DFD não localizado...'), 404);
        }

        $instance['items'] = DfdItem::where('dfd', $request->id)
            ->with('item')
            ->get();

        return Response()->json($instance, 200);
    }

    public function export(Request $request)
    {
        $instance = Dfd::with(['organ', 'unit', 'demandant', 'ordinator', 'comission'])
            ->find($request->id)
            ->toArray();

        $instance['items'] = DfdItem::where('dfd', $request->id)->with('item')->get();

        if (!$instance) {
            return Response()->json(Notify::warning('DFD não localizado...'), 404);
        }

        return Response()->json($instance, 200);
    }

    public function selects(Request $request)
    {
        if ($request->key == 'comission') {
            $comissions_members = Data::find(ComissionMember::class, ['comission' => $request->search], ['responsibility']);
            return Response()->json($comissions_members);
        }

        $generic = [
            'units' => Utils::map_select(Data::find(Unit::class, order: ['name'])),
            'prioritys' => Dfd::list_priority(),
            'hirings' => Dfd::list_hirings(),
            'acquisitions' => Dfd::list_acquisitions(),
            'categories' => CatalogItem::list_categories(),
            'responsibilitys' => ComissionMember::list_responsabilities(),
            'status' => Dfd::list_status(),
            'items_types' => CatalogItem::list_types()
        ];

        if (empty($request->key)) {
            return Response()->json($generic);
        }

        if ($request->key == 'unit') {
            $especializer = array_merge($generic, [
                'ordinators' => Utils::map_select(Data::find(Ordinator::class, [$request->key => $request->search], ['name'])),
                'demandants' => Utils::map_select(Data::find(Demandant::class, [$request->key => $request->search], ['name'])),
                'comissions' => Utils::map_select(Data::find(Comission::class, [$request->key => $request->search], ['name'])),
                'programs' => Utils::map_select(Data::find(Program::class, [$request->key => $request->search], ['name'])),
                'dotations' => Utils::map_select(Data::find(Dotation::class, [$request->key => $request->search], ['name'])),
            ]);

            return Response()->json($especializer);
        }
    }

    public function items(Request $request)
    {
        return (new CatalogItems())->list($request);
    }

    private function removeItems(Request $request)
    {
        if ($request->id) {
            $req_items = json_decode($request->items, true);
            $req_keys = array_column(array_column($req_items, 'item'), 'id');
            $dfd_items = Data::find(DfdItem::class, ['dfd' => $request->id]);

            foreach ($dfd_items as $dfd_item) {
                if (!in_array($dfd_item['item'], $req_keys)) {
                    DfdItem::destroy($dfd_item['id']);
                }
            }
        }
    }
}
