<?php declare(strict_types=1);
require('../vendor/autoload.php');

use PHPUnit\Framework\TestCase;

final class DateFormatterTest extends TestCase
{
    /** @test */
    public function get_day_of_week_from_date_str(): void
    {
        $sunday = '2021-02-28';
        $saturday = '2021-02-27';
        $this->assertSame('Sunday', DateFormatter::get_day_of_week_from_date_str($sunday));
        $this->assertSame('Saturday', DateFormatter::get_day_of_week_from_date_str($saturday));
    }

    /** @test */
    public function format_date(): void
    {
        $sunday = '2021-02-28';
        $saturday = '2021-02-27';
        $this->assertSame($sunday . ' (Sunday)', DateFormatter::format_date($sunday));
        $this->assertSame($saturday . ' (Saturday)', DateFormatter::format_date($saturday));
    }
}
