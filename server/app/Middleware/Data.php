<?php

namespace App\Middleware;

use App\Models\Organ;
use App\Models\Unit;
use App\Models\User;
use App\Security\Guardian;

class Data
{
    public static function list(string $model, array $params = [], ?array $order = null, ?array $with = null)
    {
        $user = Guardian::getUser();

        if (!is_null($user)) {

            if ((!is_null($user->organs) && !is_null($user->units)) || $user->profile == User::PRF_ADMIN) {
                $query = $model::query();

                if ($user->profile != User::PRF_ADMIN) {
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

                if ($order) {
                    $query->orderBy(...$order);
                }

                if ($with) {
                    $query->with($with);
                }

                return $query->get();
            }
        }

        return [];
    }

    private static function conditionsOrgan(User $user): array
    {
        $conditions = [];
        $organs = array_keys($user->organs ?? []);
        foreach ($organs as $organ) {
            $conditions[] = ['column' => 'id', 'operator' => '=', 'value' => $organ];
        }
        return $conditions;
    }

    private static function conditionsUnit(User $user): array
    {
        $conditions = [];
        $organs = array_keys($user->organs ?? []);
        $units = array_keys($user->units ?? []);

        foreach ($organs as $organ) {
            $conditions[] = ['column' => 'organ_id', 'operator' => '=', 'value' => $organ];
        }

        foreach ($units as $unit) {
            $conditions[] = ['column' => 'id', 'operator' => '=', 'value' => $unit];
        }

        return $conditions;
    }

    private static function conditionsUser(User $user): array
    {
        return [['column' => 'organs', 'operator' => '=', 'value' => json_encode($user->organs)]];
    }

    private static function conditionsGeneric(User $user): array
    {
        $conditions = [];

        $organs = array_keys($user->organs ?? []);
        $units = array_keys($user->units ?? []);

        foreach ($organs as $organ) {
            $conditions[] = ['column' => 'organ_id', 'operator' => '=', 'value' => $organ];
        }

        foreach ($units as $unit) {
            $conditions[] = ['column' => 'unit_id', 'operator' => '=', 'value' => $unit];
        }

        return $conditions;

    }

    private static function conditionsCustomAND(?array $params): array
    {
        $conditions = [];

        foreach ($params as $param) {
            if (!isset($param['mode']) || $param['mode'] === 'AND') {
                $conditions[] = ['column' => $param['column'], 'operator' => $param['operator'], 'value' => $param['value']];
            }
        }

        return $conditions;
    }

    private static function conditionsCustomOR(?array $params): array
    {
        $conditions = [];

        foreach ($params as $param) {

            if ($param['mode'] === 'OR') {
                $conditions[] = ['column' => $param['column'], 'operator' => $param['operator'], 'value' => $param['value']];
            }
        }

        return $conditions;
    }
}