<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PolicyOvertime extends Model
{
    use HasFactory;

    protected $fillable = [
        'policy_id',
        'ot_status',
        'ot_start',
        'ot_min_minutes',
        'ot_rate_status',
        'ot_rate',
        'ot_amount',
    ];
}
