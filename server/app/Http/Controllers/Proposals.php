<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Proposal;
use App\Utils\Notify;
use Illuminate\Http\Request;
use App\Http\Middlewares\Data;

class Proposals extends Controller
{
    public function __construct()
    {
        parent::__construct(Proposal::class, User::MOD_PRICERECORDS['module']);
    }

    public function list(Request $request)
    {
        if(is_null($request->price_record)){
            return response()->json([
                'emails' => [],
                'manual' => []
            ]);
        }

        return response()->json([
            'emails' => Data::find($this->model, [
                ['column' => 'pricerecord_id', 'operator' => '=', 'value' => $request->price_record],
                ['column' => 'modality', 'operator' => '=', 'value' => Proposal::M_MAIL]
            ], ['date_ini'], ['process', 'supplier']),
            'manual' => Data::find($this->model, [
                ['column' => 'pricerecord_id', 'operator' => '=', 'value' => $request->price_record],
                ['column' => 'modality', 'operator' => '=', 'value' => Proposal::M_MANUAL]
            ], ['date_ini'], ['process', 'author'])
        ]);


    }

    public function details(Request $request)
    {
        return $this->base_details($request, ['organ', 'process', 'supplier', 'pricerecord', 'author']);
    }

    public function download(Request $request)
    {
        $proposal = Proposal::find($request->id);

        if ($proposal && $proposal->document) {
            return response()->download(storage_path("uploads/$proposal->document"), "$proposal->id.pdf");
        }

        return response()->json(Notify::warning('Arquivo Indisponível'));
    }

    public function delete(Request $request){
        $propolsal = Proposal::firstWhere([
            ['pricerecord_id', $request->price_record],
            ['supplier_id', $request->supplier]
        ])?->delete();

        $message = $propolsal ? Notify::success('Solicitação removida...') : Notify::warning('Falha ao remover solicitação...');
        $code = $propolsal ? 200 : 500;

        return response()->json($message, $code);       
    }
}
