<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PolicySwapJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'current_policy',
        'swap_policy',
        'effect_date',
        'effect_time',
        'rollback_date',
        'rollback_time',
        'status',
    ];
}
