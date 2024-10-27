<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PolicyHolidayOvertime extends Model
{
    use HasFactory;

    protected $fillable = [
        'policy_id',
        'holiday_ot_status',
        'holiday_ot_rate',
        'holiday_ot_amount',
    ];
}
