<?php
class DateFormatter
{
    static function get_day_of_week_from_date_str($date_str): string
    {
        return date('l', strtotime($date_str));
    }

    static function format_date($date_str): string
    {
        return $date_str . ' (' . self::get_day_of_week_from_date_str($date_str) . ')';
    }
}
