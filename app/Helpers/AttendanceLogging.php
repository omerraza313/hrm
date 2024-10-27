<?php

namespace App\Helpers;

use App\Models\AttendanceLog;

class AttendanceLogging {
    public static function log($key, $value)
    {
        AttendanceLog::create([
            'key' => $key,
            'value' => json_encode($value)
        ]);
    }
}