<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutoCheckout extends Model {
    use HasFactory;

    protected $fillable = [
        'date',
        'time',
        'user_id',
        'policy_id',
        'extra_data'
    ];
}