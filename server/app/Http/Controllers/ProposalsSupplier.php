<?php

namespace App\Http\Controllers;

use App\Models\DfdItem;
use App\Utils\Dates;
use App\Utils\Notify;
use App\Models\Process;
use App\Models\Proposal;
use App\Models\PriceRecord;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Http\Middlewares\Data;
use Log;

class ProposalsSupplier extends Controller
{
    public function __construct()
    {
        parent::__construct(Proposal::class, null, false);
    }

    public function check(Request $request)
    {
        $proposal = Proposal::with(['organ', 'process', 'supplier', 'price_record'])->firstWhere('token', $request->token);

        if(is_null($proposal)){
            return response()->json(Notify::warning('Solicitação de coleta de preço não localizada....'), 404);
        }

        if($proposal->status == Proposal::S_FINISHED){
            return response()->json(Notify::warning('Solicitação de coleta de preço já enviada...'), 403);
        }

        $price_record = PriceRecord::with('organ')->find($proposal->price_record);
        if(is_null($price_record) || now()->greaterThan(Dates::ptbrToUtc($price_record->date_fin))){
            return response()->json(Notify::warning('Solicitção de registro de coleta de preço expirada...'), 403);
        }

        if($proposal->status < Proposal::S_OPENED){
            $proposal->status = Proposal::S_OPENED;
            $proposal->save();
        }


        $proposal = $proposal->toArray();
        $proposal['dfd_items'] = $this->dfdItems($proposal['process']['dfds']);

        return response()->json($proposal);
    }


    private function dfdItems(?array $dfds):array
    {
        $items = array_map(function ($id) {
            return DfdItem::where('dfd', '=', $id)->get()->toArray();
        }, array_column($dfds, 'id'));

        $result = array_reduce($items, function ($carry, $subArray) {
            foreach ($subArray as $item) {
                $key = $item['item'];
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
