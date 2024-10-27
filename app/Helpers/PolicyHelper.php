<?php

namespace App\Helpers;

class PolicyHelper {
    public static function pay_gen_type(): array
    {
        return [
            // '1' => 'Time Base',
            '2' => 'Attendance Base',
            '3' => 'Hourly Base',
        ];
    }

    public static function get_gen_type_name($index): string|int
    {
        $ary = static::pay_gen_type();
        return $ary[$index];
    }

    public static function early_arrival_policy(): array
    {
        return [
            '1' => 'Actual Time',
            '2' => 'Shift Time'
        ];
    }

    public static function get_force_timeout(): array
    {
        return [
            '1' => '01 Hour',
            '2' => '02 Hour',
            '3' => '03 Hour',
            '4' => '04 Hour',
            '5' => '05 Hour',
            '6' => '06 Hour',
            '7' => '07 Hour',
            '8' => '08 Hour',
            '9' => '09 Hour',
            '10' => '10 Hour',
        ];
    }

    public static function get_timeout_policy(): array
    {
        return [
            '1' => 'Present',
            '2' => 'Absent',
            '3' => 'Half Day',
            '4' => 'One Hour',
        ];
    }

    public static function get_timeout_policy_name($index): string|int
    {
        $ary = static::get_timeout_policy();
        return $ary[$index];
    }

    public static function get_policy_days(): array|object
    {
        return [
            '1' => 'Monday',
            '2' => 'Tuesday',
            '3' => 'Wednesday',
            '4' => 'Thursday',
            '5' => 'Friday',
            '6' => 'Saturday',
            '7' => 'Sunday',
        ];
    }

    public static function get_policy_hours(): array|object
    {
        return [
            (object) [
                'id' => '1',
                'day' => 'Monday',
                'start_time' => '',
                'end_time' => '',
                'active' => 0
            ],
            (object) [
                'id' => '2',
                'day' => 'Tuesday',
                'start_time' => '',
                'end_time' => '',
                'active' => 0
            ],
            (object) [
                'id' => '3',
                'day' => 'Wednesday',
                'start_time' => '',
                'end_time' => '',
                'active' => 0
            ],
            (object) [
                'id' => '4',
                'day' => 'Thursday',
                'start_time' => '',
                'end_time' => '',
                'active' => 0
            ],
            (object) [
                'id' => '5',
                'day' => 'Friday',
                'start_time' => '',
                'end_time' => '',
                'active' => 0
            ],
            (object) [
                'id' => '6',
                'day' => 'Saturday',
                'start_time' => '',
                'end_time' => '',
                'active' => 0
            ],
            (object) [
                'id' => '7',
                'day' => 'Sunday',
                'start_time' => '',
                'end_time' => '',
                'active' => 0
            ],
        ];
    }

    public static function get_over_time_status(): array
    {
        return [
            '1' => 'Unpaid',
            '2' => 'Paid'
        ];
    }

    public static function get_ot_status_name($index): string|int
    {
        $ary = static::get_over_time_status();
        return $ary[$index];
    }
    public static function get_policy_days_name($index): string|int
    {
        $ary = static::get_policy_days();
        return $ary[$index];
    }
    public static function early_arrival_policy_name($index): string|int
    {
        $ary = static::early_arrival_policy();
        return $ary[$index];
    }

    public static function get_over_time_rate(): array
    {
        return [
            '1' => 'Fixed Rate/hour',
            '2' => 'Equal Salary/hour',
            '3' => 'Salary/Hour Multiply X',
            '4' => 'Equal Salary/Day',
        ];
    }

    public static function get_holiday_over_time_rate(): array
    {
        return [
            '1' => 'Unpaid',
            '2' => 'Equal Salary/hour',
            '3' => 'Fixed Rate/hour',
            '4' => 'Fixed Rate/day',
            '5' => 'Salary/Hour Multiply X',
            '6' => 'Equal Salary/Day',
        ];
    }
}
