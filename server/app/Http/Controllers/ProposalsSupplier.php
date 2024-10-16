<?php

namespace App\Http\Controllers;

use App\Utils\Dates;
use App\Utils\Notify;
use App\Utils\Uploads;
use App\Models\DfdItem;
use App\Models\Proposal;
use App\Models\PriceRecord;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProposalsSupplier extends Controller
{
    /**
     * ProposalsSupplier constructor.
     */
    public function __construct()
    {
        parent::__construct(Proposal::class, null, false);
    }

    /**
     * Verifica o status de uma proposta com base no token fornecido.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function check(Request $request): JsonResponse
    {
        $proposal = Proposal::with(['organ', 'process', 'supplier', 'pricerecord'])
            ->firstWhere('token', $request->token);

        if (is_null($proposal)) {
            return response()->json(Notify::warning('Solicitação de coleta de preço não localizada....'), 404);
        }

        if ($proposal->status == Proposal::S_FINISHED) {
            return response()->json(Notify::success('Solicitação de coleta de preço já enviada...'), 403);
        }

        $pricerecord = PriceRecord::with('organ')->find($proposal->pricerecord_id);
        if (is_null($pricerecord) || now()->greaterThan(Dates::ptbrToUtc($pricerecord->date_fin))) {
            return response()->json(Notify::warning('Solicitção de registro de coleta de preço expirada...'), 403);
        }

        if ($proposal->status < Proposal::S_OPENED) {
            $proposal->status = Proposal::S_OPENED;
            $proposal->save();
        }

        $proposalArray = $proposal->toArray();
        $proposalArray['dfd_items'] = $this->dfdItems($proposalArray['process']['dfds']);

        return response()->json($proposalArray);
    }

    /**
     * Salva os dados de uma proposta com as validações necessárias.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function save(Request $request): JsonResponse
    {
        $instance = $this->model::find($request->id);

        if (is_null($instance)) {
            return response()->json(Notify::warning('Solicitação de coleta não localizada...'), 404);
        }

        if ($request->status == Proposal::S_FINISHED) {
            if (is_null($request->document)) {
                return response()->json(Notify::warning('É necessário anexar a proposta assinada para confirmar o envio...'), 400);
            }

            if (is_null($request->code) || $request->code != $instance->protocol) {
                return response()->json(Notify::warning('Código de validação incorreto...'), 403);
            }
        }

        // Gerencia o upload de documentos
        $upload = new Uploads($request, ['document' => ['nullable' => true]]);
        if ($request->id && $request->hasFile('document')) {
            $upload->remove($instance->document);
        }

        $dataToUpdate = array_merge($request->all(), [
            'date_fin' => date('d/m/Y'),
            'hour_fin' => date('H:i:s')
        ]);

        if ($instance->update($upload->mergeUploads($dataToUpdate))) {
            PriceRecords::updateStatusPriceRecord($instance->pricerecord_id);
            return response()->json(Notify::success('Proposta salva com sucesso'), 200);
        } else {
            return response()->json(Notify::error('Falha ao gravar proposta'), 500);
        }
    }

    /**
     * Retorna uma lista de itens do DFD somados por quantidade.
     *
     * @param array|null $dfds
     * @return array
     */
    public function dfdItems(?array $dfds): array
    {
        $items = array_map(function ($id) {
            return DfdItem::where('dfd_id', '=', $id)->with('item')->get()->toArray();
        }, array_column($dfds, 'id'));

        $result = array_reduce($items, function ($carry, $subArray) {
            foreach ($subArray as $item) {
                $key = $item['item']['id'];
                if (isset($carry[$key])) {
                    $carry[$key]['quantity'] += $item['quantity'];
                } else {
                    $carry[$key] = $item;
                }
            }
            return $carry;
        }, []);

        return array_values($result);
    }

}
