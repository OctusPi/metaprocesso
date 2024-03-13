<?php

namespace Tests\Unit;

use App\Utils\Dates;
use Carbon\Carbon;
use DateException;
use PHPUnit\Framework\TestCase;

class DatesUtilTest extends TestCase
{
    public function test_nowutc(): void
    {
        $this->assertTrue(
            Carbon::createFromFormat(Dates::UTC_TIME, Dates::nowUTC())->isValid()
        );
    }

    public function test_convert(): void
    {
        $dateUtc = "2023-12-12";
        $dateTimeUtc = "2023-12-12 12:12:12";
        $datePtbr = "12/12/2023";
        $dateTimePtbr = "12/12/2023 12:12:12";
        $nullDate = null;

        $this->assertEquals(Dates::convert($dateUtc, Dates::UTC, Dates::PTBR), $datePtbr);
        $this->assertEquals(Dates::convert($datePtbr, Dates::PTBR, Dates::UTC), $dateUtc);
        $this->assertEquals(Dates::convert($dateTimeUtc, Dates::UTC_TIME, Dates::PTBR_TIME), $dateTimePtbr);
        $this->assertEquals(Dates::convert($dateTimePtbr, Dates::PTBR_TIME, Dates::UTC_TIME), $dateTimeUtc);
        $this->assertEquals(Dates::convert($nullDate, Dates::UTC, Dates::PTBR), $nullDate);
    }

    public function test_utc_invalid(): void
    {
        $this->expectException(DateException::class);

        $dateUtc = "2023/12/12";

        Dates::convert($dateUtc, Dates::UTC, Dates::PTBR);

    }

    public function test_ptbr_invalid(): void
    {
        $this->expectException(DateException::class);

        $datePtbr = "2023/12/12";

        Dates::convert($datePtbr, Dates::PTBR, Dates::UTC);

    }
}
