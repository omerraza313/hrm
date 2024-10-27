<?php

namespace App\Helpers;

use App\Models\Logging;

class LoggingHelper {
    public static function log($key, $value)
    {
        Logging::create([
            'key' => $key,
            'value' => json_encode($value)
        ]);
    }
}