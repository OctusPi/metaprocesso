<?php

namespace App\Http\Controllers;

use App\Models\Etp;
use App\Models\PriceRecord;
use App\Models\RiskMap;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Dfd;
use App\Models\Process;
use App\Http\Middlewares\Data;

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

        $processesQ = Data::find(new Process(), [], ['date_hour_ini'], [
            'priceRecord' => fn ($query) => $query->where('status', '=', PriceRecord::S_FINISHED),
            'etp' => fn ($query) =>  $query->where('status', '=', Etp::S_FINISHED),
            'riskmaps' => fn ($query) => $query->where('status', '=', RiskMap::S_FINISHED),
            'refterm' => fn ($query) => $query->get(),
        ]);

        $processes = (object) [];
        $processesQ->each(function (Process $item) use ($processes) {
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

        $user = $request->user();

        $profiles = User::list_profiles();
        $profiles = array_map(fn($key, $value) => ['id' => $key, 'title' => $value],
            array_keys($profiles),
            $profiles
        );

        return response()->json([
            'dfds_chart' => $dfds,
            'processes_chart' => $processes,
            'prices_chart' => $prices,
            'processes' => [
                'datalist' => $processesQ,
                'selects' => [
                    'types' => Process::list_types(),
                    'modalities' => Process::list_modalitys(),
                ]
            ],
            'users' => [
                'datalist' => Data::find(new User(), [])->where('profile', '>=', $user->profile),
                'selects' => [
                    'profiles' => $profiles,
                ]
            ],
        ]);
    }
}
