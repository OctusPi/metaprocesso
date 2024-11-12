<?php

namespace App\Http\Controllers;

use App\Http\Middlewares\Data;
use App\Models\Comission;
use App\Models\Dfd;
use App\Models\DfdItem;
use App\Models\Dotation;
use App\Models\Pca;
use App\Models\Program;
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

        $dfds = Data::query(
            new Dfd(),
            ['year_pca' => $request->year],
            ['date_ini'],
            ['demandant', 'ordinator', 'unit'],
        )->whereNotIn('status', [Dfd::STATUS_RASCUNHO, Dfd::STATUS_BLOQUEADO])->get();

        $dfdsChart = (object) [];
        $dfds->each(function (Dfd $item) use ($dfdsChart) {
            $key = Utils::getSelect(Dfd::list_acquisitions(), $item->acquisition_type);
            if (isset($dfdsChart->{$key})) {
                $dfdsChart->{$key}['num']++;
                $dfdsChart->{$key}['price'] += Utils::toFloat($item->estimated_value);
            } else {
                $dfdsChart->{$key}['num'] = 1;
                $dfdsChart->{$key}['price'] = Utils::toFloat($item->estimated_value);
            }
        });

        return response()->json([
            'datalist' => $dfds,
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
            'comissions' => Utils::map_select(Data::find(new Comission(), ['status' => Comission::STATUS_ACTIVE], ['name']))
        ]));
    }

    /**
     * Exporta dados de PCA e DFDs.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function export(Request $request)
    {
        // Obtenha os detalhes básicos do PCA com informações relacionadas
        $pca = $this->base_details($request, ['organ', 'comission']);

        // Verifique se o status do PCA é 200 antes de continuar
        if ($pca->status() !== 200) {
            return response()->json(Notify::warning("DFDs não localizados..."), $pca->status());
        }

        $data = $pca->getData(true);

        // Query para buscar DFDs com filtros e exclusões de status
        $dfds = Data::query(
            new Dfd(),
            ['year_pca' => $data['reference_year']],
            ['date_ini'],
            ['demandant', 'ordinator', 'unit'])
            ->whereNotIn('status', [Dfd::STATUS_RASCUNHO, Dfd::STATUS_BLOQUEADO])
            ->get();

        // Verifica se há DFDs antes de buscar os itens relacionados
        $items = [];
        if ($dfds->isNotEmpty()) {
            // Obtenha os itens dos DFDs com os IDs coletados
            $dfdIds = $dfds->pluck('id');
            $items = Data::query(new DfdItem(), with:['item', 'program', 'dotation'])
                ->whereIn('dfd_id', $dfdIds)
                ->get()
                ->sortBy('item.name')
                ->values()
                ->toArray();
        }



        // Combine dados do PCA e resultados das queries em um único array de resposta
        return response()->json(array_merge($data, ['dfds' => $dfds->toArray(), 'items' => $items]), 200);
    }

}
