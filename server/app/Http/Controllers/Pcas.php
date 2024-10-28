<?php

namespace App\Http\Controllers;

use App\Http\Middlewares\Data;
use App\Models\Comission;
use App\Models\Dfd;
use App\Models\DfdItem;
use App\Models\Pca;
use App\Models\User;
use App\Utils\Notify;
use App\Utils\Utils;
use Carbon\Carbon;
use Illuminate\Http\Request;

class Pcas extends Controller
{
    public function __construct()
    {
        parent::__construct(Pca::class, User::MOD_PCA['module']);
    }

    public function list(Request $request)
    {
        return $this->base_list(
            $request,
            ['reference_year', 'status', 'comission_id'],
            ['reference_year', 'desc'],
            ['comission'],
        );
    }

    public function save(Request $request)
    {
        $comission = Data::findOne(new Comission(), ['id' => $request->comisison_id]);

        if (!$comission) {
            return response()->json(Notify::warning('Comissão não encontrada'), 404);
        }

        return $this->base_save($request, [
            'ip' => $request->ip(),
            'author_id' => $request->user()->id,
            'comission_members' => $comission->comissionmembers
        ]);
    }

    public function list_dfds(Request $request)
    {
        if (!$request->year) {
            return response()->json('Insira o ano de referência', 400);
        }

        $dateRange = [
            'date_ini' => [
                Carbon::create($request->year, 1, 1),
                Carbon::create($request->year, 12, 31)
            ]
        ];
        
        $dfds = Data::query(
            new Dfd(),
            with: ['demandant', 'ordinator', 'unit'],
            between: $dateRange
        )->whereNotIn(
                'status',
                [Dfd::STATUS_RASCUNHO, Dfd::STATUS_BLOQUEADO],
            )->get();

        $estimated = $dfds->reduce(function (?float $val, $dfd) {
            return $val + Utils::toFloat($dfd->estimated_value);
        });

        $dfdsChart = (object) [];
        $dfds->each(function (Dfd $item) use ($dfdsChart) {
            $key = match ($item->status) {
                Dfd::ACQUISITION_MATERIAL_CONSUMO,
                Dfd::ACQUISITION_MATERIAL_PERMANENTE => 'Material',
                default => 'Serviço'
            };

            if (isset($dfdsChart->{$key})) {
                $dfdsChart->{$key}++;
            } else {
                $dfdsChart->{$key} = 1;
            }
        });

        return response()->json([
            'datalist' => $dfds,
            'estimated' => $estimated ?? 0,
            'dfds_chart' => $dfdsChart,
        ]);
    }

    public function list_dfd_items(Request $request)
    {
        return response()->json(
            Data::find(new DfdItem(), ['dfd_id' => $request->id], null, ['item', 'dotation', 'program']),
            200
        );
    }

    public function selects(Request $request)
    {
        return response()->json(array_merge(Dfd::make_details(), [
            'status' => Pca::list_status(),
            'comissions' => Utils::map_select(
                Data::find(new Comission(), ['status' => Comission::STATUS_ACTIVE], ['name'])
            ),
        ]));
    }
}
