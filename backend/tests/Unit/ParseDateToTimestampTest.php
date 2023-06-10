<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Helpers\Services\DateTimeService;

class ParseDateToTimestampTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_parses_date_to_timestamp(): void
    {
        list($start, $end) = DateTimeService::parseDateToTimezone(
            '01-01-2023',
            '01-10-2023',
            'Europe/Amsterdam'
        );

        $this->assertIsInt($start);
        $this->assertIsInt($end);
        $this->assertEquals(1672527600, $start);
        $this->assertEquals(1696197599, $end);
    }
}
