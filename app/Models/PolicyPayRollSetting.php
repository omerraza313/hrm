<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PolicyPayRollSetting extends Model
{
    use HasFactory;
    protected $fillable = [
        'policy_id',
        'generation_type',
        'off_days_per_month',
        'working_hours',
        'minutes',
        'max_shift_retaining_hours'
    ];
}
