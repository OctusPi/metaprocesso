<?php

namespace App\Middleware;

use App\Models\Common;
use App\Models\Unit;
use App\Models\User;
use App\Models\Organ;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class Data
{
    public static function query(string $model, array $params = [], ?array $order = null, ?array $with = null, ?array $between = null, ?array $objects = null)
    {
        $user = Auth::user();

        if (!is_null($user)) {

            if ((!is_null($user->organs) && !is_null($user->units)) || $user->profile == Common::PRF_ADMIN) {
                $query = $model::query();

                if ($user->profile != Common::PRF_ADMIN) {
                    $conditionsUser = match ($model) {
                        Organ::class => self::conditionsOrgan($user),
                        Unit::class => self::conditionsUnit($user),
                        User::class => self::conditionsUser($user),
                        default => self::conditionsGeneric($user),
                    };

                    $query->where(function ($query) use ($conditionsUser) {
                        foreach ($conditionsUser as $condition) {
                            $query->where($condition['column'], $condition['operator'], $condition['value']);
                        }
                    });
                }

                $conditionsAnd = self::conditionsCustomAND($params);
                if (!empty($conditionsAnd)) {
                    $query->where(function ($query) use ($conditionsAnd) {
                        foreach ($conditionsAnd as $condition) {
                            $query->where($condition['column'], $condition['operator'], $condition['value']);
                        }
                    });
                }

                $conditionsOr = self::conditionsCustomOR($params);
                if (!empty($conditionsOr)) {
                    $query->where(function ($query) use ($conditionsOr) {
                        foreach ($conditionsOr as $condition) {
                            $query->orWhere($condition['column'], $condition['operator'], $condition['value']);
                        }
                    });
                }


                //apply between
                if($between){
                    foreach ($between as $key => $value) {
                        $query->whereBetween($key, $value);
                    }
                }

                // apply objects
                if($objects){
                    foreach ($objects as $key => $value) {
                        $query->whereJsonContains($key, $value);
                    }
                }

                //apply order
                if ($order) {
                    $query->orderBy(...$order);
                }

                //apply with
                if ($with) {
                    $query->with($with);
                }

                return $query;
            }
        }

        return null;
    }

    public static function list(string $model, array $params = [], ?array $order = null, ?array $with = null, ?array $between = null, ?array $objects = null)
    {
        $query = self::query($model, $params, $order, $with, $between, $objects);
        if(!is_null($query)){
            return $query->get();
        }

        return [];
    }

    public static function find(string $model, array $params = [], ?array $order = null, ?array $with = null){
        $query = self::query($model, $params, $order, $with);
        if(!is_null($query)){
            return $query->first();
        }

        return null;
    }

    private static function conditionsOrgan($user): array
    {
        $conditions = [];
        $organs = array_keys($user->organs ?? []);
        foreach ($organs as $organ) {
            $conditions[] = ['column' => 'id', 'operator' => '=', 'value' => $organ];
        }
        return $conditions;
    }

    private static function conditionsUnit($user): array
    {
        $conditions = [];
        $organs = array_keys($user->organs ?? []);
        $units = array_keys($user->units ?? []);

        foreach ($organs as $organ) {
            $conditions[] = ['column' => 'organ', 'operator' => '=', 'value' => $organ];
        }

        foreach ($units as $unit) {
            $conditions[] = ['column' => 'id', 'operator' => '=', 'value' => $unit];
        }

        return $conditions;
    }

    private static function conditionsUser($user): array
    {
        return [['column' => 'organs', 'operator' => '=', 'value' => json_encode($user->organs)]];
    }

    private static function conditionsGeneric($user): array
    {
        $conditions = [];

        $organs = array_column($user->organs ?? [], 'id');
        $units = array_column($user->units ?? [], 'id');

        foreach ($organs as $organ) {
            $conditions[] = ['column' => 'organ', 'operator' => '=', 'value' => $organ];
        }

        foreach ($units as $unit) {
            $conditions[] = ['column' => 'unit', 'operator' => '=', 'value' => $unit];
        }

        return $conditions;

    }

    private static function conditionsCustomAND(?array $params): array
    {
        $conditions = [];

        foreach ($params as $key => $param) {
            if(is_array($param)){
                if (!isset($param['mode']) || $param['mode'] === 'AND') {
                    $conditions[] = [
                        'column' => $param['column'] ?? $param[0], 
                        'operator' => $param['operator'] ?? 'LIKE', 
                        'value' => $param['value'] ?? $param[1]
                    ];
                }
            }else{
                $conditions[] = [
                    'column' => $key, 
                    'operator' => 'LIKE', 
                    'value' => $param
                ];
            }
            
        }

        return $conditions;
    }

    private static function conditionsCustomOR(?array $params): array
    {
        $conditions = [];

        foreach ($params as $param) {

            if(is_array($param)){
                if (isset($param['mode']) && $param['mode'] === 'OR') {
                    $conditions[] = [
                        'column' => $param['column'] ?? $params[0], 
                        'operator' => $param['operator'] ?? 'LIKE', 
                        'value' => $param['value'] ?? $param[1]
                    ];
                }
            }
        }

        return $conditions;
    }
}