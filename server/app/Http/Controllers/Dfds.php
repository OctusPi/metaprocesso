<?php

namespace App\Http\Controllers;

use App\Models\CatalogItem;
use App\Models\ComissionMember;
use App\Models\Dfd;
use App\Models\DfdItem;
use App\Models\Dotation;
use App\Models\Program;
use App\Models\Unit;
use App\Models\User;
use App\Models\Organ;
use App\Utils\Notify;
use App\Utils\Utils;
use GuzzleHttp\Client;
use App\Middleware\Data;
use App\Models\Comission;
use App\Models\Demandant;
use App\Models\Ordinator;
use App\Security\Guardian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Dfds extends Controller
{
    public function __construct()
    {
        parent::__construct(Dfd::class, User::MOD_DFDS);
        Guardian::validateAccess($this->module_id);
    }

    public function save(Request $request)
    {

        $ip = $request->ip();
        $code = Utils::randCode(6, str_pad($request->unit, 3, '0', STR_PAD_LEFT), date('dmY'));
        $data = array_merge($request->all(), ['ip' => $ip, 'protocol' => $code, 'author' => $this->user_loged->id]);
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
        return $this->baseList(['organ', 'unit', 'protocol', 'date_ini'], ['date_ini'], ['organ', 'unit', 'demandant', 'ordinator']);
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
        //feed selects form
        if ($request->key != 'comission') {
            $units = [];
            $ordinators = [];
            $demandants = [];
            $comissions = [];
            $programs = [];
            $dotations = [];

            if ($request->key) {
                $units = $request->key == 'organ' ? Utils::map_select(Data::list(Unit::class, [
                    [
                        'column' => $request->key,
                        'operator' => '=',
                        'value' => $request->search,
                        'mode' => 'AND'
                    ]
                ], ['name'])) : Utils::map_select(Data::list(Unit::class));

                $ordinators = Utils::map_select(Data::list(Ordinator::class, [
                    [
                        'column' => $request->key,
                        'operator' => '=',
                        'value' => $request->search,
                        'mode' => 'AND'
                    ]
                ], ['name']));

                $demandants = Utils::map_select(Data::list(Demandant::class, [
                    [
                        'column' => $request->key,
                        'operator' => '=',
                        'value' => $request->search,
                        'mode' => 'AND'
                    ]
                ], ['name']));

                $comissions = Utils::map_select(Data::list(Comission::class, [
                    [
                        'column' => $request->key,
                        'operator' => '=',
                        'value' => $request->search,
                        'mode' => 'AND'
                    ]
                ], ['name']));

                $programs = Utils::map_select(Data::list(Program::class, [
                    [
                        'column' => $request->key,
                        'operator' => '=',
                        'value' => $request->search,
                        'mode' => 'AND'
                    ]
                ], ['name']));

                $dotations = Utils::map_select(Data::list(Dotation::class, [
                    [
                        'column' => $request->key,
                        'operator' => '=',
                        'value' => $request->search,
                        'mode' => 'AND'
                    ]
                ], ['name']));
            }

            return Response()->json([
                'organs' => Utils::map_select(Data::list(Organ::class, order: ['name'])),
                'units' => $units,
                'ordinators' => $ordinators,
                'demandants' => $demandants,
                'comissions' => $comissions,
                'prioritys' => Dfd::list_priority(),
                'hirings' => Dfd::list_hirings(),
                'acquisitions' => Dfd::list_acquisitions(),
                'programs' => $programs,
                'dotations' => $dotations,
                'categories' => CatalogItem::list_categoria(),
                'responsibilitys' => ComissionMember::list_responsabilities(),
                'status' => Dfd::list_status()
            ], 200);
        }

        //rescue comission_members
        $comissions_members = Data::list(ComissionMember::class, ['comission' => $request->search], ['responsibility']);
        return Response()->json($comissions_members);

    }

    public function generate(Request $request)
    {
        $api_key = getenv('OPENIA_KEY');
        $client = new Client();
        $url = 'https://api.openai.com/v1/completions';
        $data = [
            'model' => 'gpt-3.5-turbo-instruct',
            'prompt' => $request->payload,
            'max_tokens' => 512
        ];

        try {

            $resp = $client->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $api_key,
                    'Content-Type' => 'application/json'
                ],
                'json' => $data
            ]);

            return Response()->json(json_decode($resp->getBody()), 200);

        } catch (\Exception $e) {
            Log::alert('Falha ao receber dados da API: ' . $e->getMessage());
            return Response()->json(Notify::warning('Falha ao receber dados da API'), 400);
        }
    }

    public function items(Request $request)
    {
        return (new CatalogItems())->list($request);
    }
}
