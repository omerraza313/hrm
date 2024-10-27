<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyContact extends Model
{
    use HasFactory;

    protected $table = 'family_contacts';

    protected $fillable = [
        'name',
        'number',
        'relation',
        'user_id',
        'ice_status',
        'dob'
    ];
}
