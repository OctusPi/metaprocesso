<?php

namespace App\Http\Controllers;

use App\Models\PriceRecord;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Dfd;
use App\Models\Process;
use App\Http\Middlewares\Data;
use stdClass;

class Home extends Controller
{
    public function __construct()
    {
        parent::__construct(null, User::MOD_INI['module']);
    }

    private function verbose(array $arr, int $item)
    {
        $index = array_search($item, array_column($arr, 'id'));
        return $arr[$index]['title'];
    }

    public function list(Request $request)
    {
        $dfds = (object) [];
        Data::find(new Dfd(), [])
            ->each(function (Dfd $item) use ($dfds) {
                $key = $this->verbose(
                    Dfd::list_status(),
                    $item->status
                );
                if (isset($dfds->{$key})) {
                    $dfds->{$key}++;
                } else {
                    $dfds->{$key} = 1;
                }
            });

        $processes = (object) [];
        Data::find(new Process(), [])
            ->each(function (Process $item) use ($processes) {
                $key = $this->verbose(
                    Process::list_status(),
                    $item->status
                );
                if (isset($processes->{$key})) {
                    $processes->{$key}++;
                } else {
                    $processes->{$key} = 1;
                }
            });

        $prices = (object) [];
        Data::find(new PriceRecord(), [])
            ->each(function (PriceRecord $item) use ($prices) {
                $key = $this->verbose(
                    PriceRecord::list_status(),
                    $item->status
                );
                if (isset($prices->{$key})) {
                    $prices->{$key}++;
                } else {
                    $prices->{$key} = 1;
                }
            });

        return response()->json([
            'dfds_chart' => $dfds,
            'processes_chart' => $processes,
            'prices_chart' => $prices,
            'processes' => [
                'datalist' => Data::find(new Process(), [], ['date_hour_ini'], [
                    'pricerecord',
                    'etp',
                    'riskmaps',
                    'refterm',
                ]),
                'selects' => [
                    'types' => Process::list_types(),
                    'modalities' => Process::list_modalitys(),
                ]
            ],
            'users' => [
                'datalist' => Data::find(new User(), [], ['lastlogin']),
            ],
        ]);
    }
}
