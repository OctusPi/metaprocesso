<?php

namespace App\Utils;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;


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

    public static function map_select(Collection $list, string $display = 'name')
    {
        if ($list) {
            return $list->map(function (Model $item) use ($display) {
                return ['id' => (int) $item->id, 'title' => $item->$display];
            });
        }
        return [];
    }

    public static function randCode(int $size = 4,  string $prefix = '', string $sufix = ''){
        $char = 'ABCDFGHIJLMNKOPQRSTUYWXZ0123456789';
        $code = '';
        for($i=0; $i<$size; $i++){
            $code .= $char[random_int(0, strlen($char)-1)];
        }

        return $prefix.'-'.$code.'-'.$sufix;
    }
}
