<?php

namespace App\Helpers;

class ProfileHelper
{
    public static function get_education_subject(): array|object
    {
        return [
            '1' => 'Marketing',
            '2' => 'Finance',
            '3' => 'Accounting',
            '4' => 'HR',
            '5' => 'IT',
            '6' => 'Software Engineering',
            '7' => 'Other',
        ];
    }
}
