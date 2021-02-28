<?php declare(strict_types=1);
require('../src/date_format.php');

use PHPUnit\Framework\TestCase;

final class date_format_test extends TestCase
{
    /** @test */
    public function get_day_of_week_from_date_str(): void
    {
        $sunday = '2021-02-28';
        $saturday = '2021-02-27';
        $this->assertSame('Sunday', get_day_of_week_from_date_str($sunday));
        $this->assertSame('Saturday', get_day_of_week_from_date_str($saturday));
    }

    /** @test */
    public function format_date(): void
    {
        $sunday = '2021-02-28';
        $saturday = '2021-02-27';
        $this->assertSame($sunday . ' (Sunday)', format_date($sunday));
        $this->assertSame($saturday . ' (Saturday)', format_date($saturday));
    }
}
