<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model {
    use HasFactory, SoftDeletes;

    protected $table = 'departments';

    protected $fillable = [
        'name'
    ];

    public function designations()
    {
        return $this->hasMany(Designation::class);
    }
}