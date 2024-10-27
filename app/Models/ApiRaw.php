<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiRaw extends Model {
    use HasFactory;

    protected $fillable = [
        'method',
        'path',
        'ip',
        'data',
        'imported',
    ];
}
