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

    public Carbon $date;

    /**
     * Creates a Carbon date instance
     * @param string $format Defines the date format
     * @param string $date Defines the date string with the given **$format** param
     */

    public function __construct(string $format, string $date)
    {
        if (!Carbon::canBeCreatedFromFormat($date, $format)) {
            throw new DateException("The given date does not match the format");
        }

        $this->date = Carbon::createFromFormat($format, $date, "America/Fortaleza");
    }

    /**
     * Returns the current UTC datetime value
     * @return string
     */
    public static function nowUTC(): string
    {
        return Carbon::now('UTC')->toDateTimeString();
    }

    /**
     * Converts a Pt-BR date string to UTC format
     * @param string $to Defines the expected format
     * @return string
     */
    public function convertTo(string $to): string
    {
        return $this->date->format($to);
    }
}
