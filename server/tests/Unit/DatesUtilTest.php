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
        $datePtbr = "12/12/2023";

        $this->assertEquals(Dates::utcToPtbr($dateUtc), $datePtbr);
        $this->assertEquals(Dates::ptbrToUtc($datePtbr), $dateUtc);
    }

    public function test_utc_invalid(): void
    {
        $this->expectException(DateException::class);
        
        $dateUtc = "2023/12/12";

        Dates::utcToPtbr($dateUtc);
    }

    public function test_ptbr_invalid(): void
    {
        $this->expectException(DateException::class);
        
        $datePtbr = "2023/12/12";

        Dates::ptbrToUtc($datePtbr);
    }
}
