<?php

namespace App\Utils;

use Carbon\Carbon;

class Dates
{
    public static function nowUTC(): string
    {
        return Carbon::now('UTC')->toDateTimeString();
    }
}
