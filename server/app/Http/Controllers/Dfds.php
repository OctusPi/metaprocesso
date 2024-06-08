<?php

namespace App\Http\Controllers;

use App\Models\CatalogItem;
use App\Models\Dfd;
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

    public function list(Request $request)
    {
        return $this->baseList(['organ', 'unit', 'name'], ['date_ini'], ['organ', 'unit']);
    }

    public function selects(Request $request)
    {
        $units      = [];
        $ordinators = [];
        $demandants = [];
        $comissions = [];
        $programs   = [];
        $dotations  = [];
        
        if($request->key){
            $units = $request->key == 'organ' ? Utils::map_select(Data::list(Unit::class, [
                [
                    'column'   => $request->key,
                    'operator' => '=',
                    'value'    => $request->search,
                    'mode'     => 'AND'
                ]
                ], ['name'])) : Utils::map_select(Data::list(Unit::class));

            $ordinators = Utils::map_select(Data::list(Ordinator::class, [
                [
                    'column'   => $request->key,
                    'operator' => '=',
                    'value'    => $request->search,
                    'mode'     => 'AND'
                ]
                ], ['name']));
            
            $demandants = Utils::map_select(Data::list(Demandant::class, [
                [
                    'column'   => $request->key,
                    'operator' => '=',
                    'value'    => $request->search,
                    'mode'     => 'AND'
                ]
                ], ['name']));

            $comissions = Utils::map_select(Data::list(Comission::class, [
                [
                    'column'   => $request->key,
                    'operator' => '=',
                    'value'    => $request->search,
                    'mode'     => 'AND'
                ]
                ], ['name']));
            
            $programs = Utils::map_select(Data::list(Program::class, [
                    [
                        'column'   => $request->key,
                        'operator' => '=',
                        'value'    => $request->search,
                        'mode'     => 'AND'
                    ]
                    ], ['name']));
            
            $dotations = Utils::map_select(Data::list(Dotation::class, [
                        [
                            'column'   => $request->key,
                            'operator' => '=',
                            'value'    => $request->search,
                            'mode'     => 'AND'
                        ]
                        ], ['name']));
        }
       

        return Response()->json([
            'organs'       => Utils::map_select(Data::list(Organ::class, order:['name'])),
            'units'        => $units,
            'ordinators'   => $ordinators,
            'demandants'   => $demandants,
            'comissions'   => $comissions,
            'prioritys'    => Dfd::list_priority(),
            'hirings'      => Dfd::list_hirings(),
            'acquisitions' => Dfd::list_acquisitions(),
            'bonds'        => Dfd::list_bonds(),
            'programs'     => $programs,
            'dotations'    => $dotations,
            'categories'   => CatalogItem::list_categoria()
        ], 200);
    }

    public function generate(Request $request)
    {
        $api_key = getenv('OPENIA_KEY');
        $client  = new Client();
        $url  = 'https://api.openai.com/v1/completions';
        $data = [
            'model'  => 'gpt-3.5-turbo-instruct',
            'prompt' => $request->payload,
            'max_tokens' => 256
        ];

        try{

            $resp = $client->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer '.$api_key,
                    'Content-Type'  => 'application/json'
                ],
                'json' => $data
            ]);

            return Response()->json(json_decode($resp->getBody()), 200);

        }catch(\Exception $e){
            Log::alert('Falha ao receber dados da API: '.$e->getMessage());
            return Response()->json(Notify::warning('Falha ao receber dados da API'), 400);
        }
    }

    public function items(Request $request)
    {
        return (new CatalogItems())->list($request);
    }
}
