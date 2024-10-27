<?php

namespace App\Helpers;

use Carbon\Carbon;

class EmployeeHelper {
    public static function get_reasons(): array
    {
        return [
            '1' => 'Personal Reason',
            '2' => 'Joining Another Company',
            '3' => 'Going Abroad',
            '4' => 'Professional Dispute',
            '5' => 'Underperformance',
            '6' => 'Terminate',
        ];
    }

    public static function get_reasons_name($index)
    {
        $ary = static::get_reasons();
        return $ary[$index];
    }
}
