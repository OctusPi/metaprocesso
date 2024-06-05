<?php

namespace App\Utils;

use Illuminate\Http\Request;
use Illuminate\Routing\RouteParameterBinder;


class Utils
{
    public static function map_search(array $search, array $subject, string $mode = 'AND'): array
    {
        $map = [];

        function map_operator($value)
        {
            return match (gettype($value)) {
                'string' => 'LIKE',
                'array' => 'BETWEEN',
                default => '=',
            };
        }

        function map_value($value)
        {
            return match (gettype($value)) {
                'string' => "%$value%",
                'array' => implode(' AND ', $value),
                default => $value,
            };
        }

        foreach ($subject as $key => $value) {
            if (in_array($key, $search) && !empty($value)) {
                $map[] = ['column' => $key, 'operator' => map_operator($value), 'value' => map_value($value), 'mode' => $mode];
            }
        }

        return $map;
    }

    public static function map_select($list)
    {

        if ($list) {
            return $list->map(function ($item) {
                return ['id' => (int) $item->id, 'title' => $item->name];
            });
        }
        return [];
    }
}
