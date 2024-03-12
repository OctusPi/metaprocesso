<?php

namespace App\Utils;

use Carbon\Carbon;
use DateException;

class Dates
{
    const UTC = "Y-m-d";
    const UTC_TIME = "Y-m-d H:i:s";
    const PTBR = "d/m/Y";
    const PTBR_TIME = "d/m/Y H:i:s";

    /**
     * Returns the current UTC datetime value
     * @return string
     */
    public static function nowUTC(): string
    {
        return Carbon::now('UTC')->toDateTimeString();
    }

    /**
     * Converts a UTC date string to PTBR format
     * 
     * @param string $utcDate Defines the date in the UTC format
     * 
     * @return string
     */
    public static function utcToPtBr(string $utcDate): string
    {
        if (!Carbon::canBeCreatedFromFormat($utcDate, self::UTC)) {
            throw new DateException("The given date does not match the UTC format");
        }

        return Carbon::createFromFormat(self::UTC, $utcDate)
            ->format(self::PTBR);
    }

    /**
     * Converts a PTRB date string to UTC format
     * 
     * @param string $ptbrDate Defines the date in the PTBR format
     * 
     * @return string
     */
    public static function ptbrToUtc(string $ptbrDate): string
    {
        if (!Carbon::canBeCreatedFromFormat($ptbrDate, self::PTBR)) {
            throw new DateException("The given date does not match the PTBR format");
        }

        return Carbon::createFromFormat(self::PTBR, $ptbrDate)
            ->format(self::UTC);
    }
}
