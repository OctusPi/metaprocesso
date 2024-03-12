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
        $dateUtc = new Dates(Dates::UTC, "2023-12-12");
        $dateTimeUtc = new Dates(Dates::UTC_TIME, "2023-12-12 12:12:12");
        $datePtbr = new Dates(Dates::PTBR, "12/12/2023");
        $dateTimePtbr = new Dates(Dates::PTBR_TIME, "12/12/2023 12:12:12");


        $this->assertEquals($dateUtc->convertTo(Dates::PTBR), "12/12/2023");
        $this->assertEquals($dateTimeUtc->convertTo(Dates::PTBR), "12/12/2023");
        $this->assertEquals($datePtbr->convertTo(Dates::UTC), "2023-12-12");
        $this->assertEquals($dateTimePtbr->convertTo(Dates::UTC), "2023-12-12");
    }

    public function test_invalid_convert_utc(): void
    {
        $this->expectException(DateException::class);

        $invalidDateUtc = new Dates(Dates::UTC, "10/20");
        $invalidDateUtc->convertTo(Dates::PTBR);
    }

    public function test_invalid_convert_ptbr(): void
    {
        $this->expectException(DateException::class);

        $invalidDatePtbr = new Dates(Dates::PTBR, "12/12");
        $invalidDatePtbr->convertTo(Dates::UTC);
    }
}
