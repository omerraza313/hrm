<?php

namespace App\Helpers;

class LeaveHelper {
    public static function getMonths(): array
    {
        return [
            'january' => 1,
            'february' => 2,
            'march' => 3,
            'april' => 4,
            'may' => 5,
            'june' => 6,
            'july' => 7,
            'august' => 8,
            'september' => 9,
            'october' => 10,
            'november' => 11,
            'december' => 12,
        ];
    }

    public static function getMonthsFromNumbers(): array
    {
        return [
            1 => 'january',
            2 => 'february',
            3 => 'march',
            4 => 'april',
            5 => 'may',
            6 => 'june',
            7 => 'july',
            8 => 'august',
            9 => 'september',
            10 => 'october',
            11 => 'november',
            12 => 'december',
        ];

    }

    public static function get_month_number($month)
    {
        $months = self::getMonths();
        return $months[$month];
    }

    public static function get_month_name($month)
    {
        $months = self::getMonthsFromNumbers();
        return $months[$month];
    }
}