<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    use HasFactory;

    protected $table = 'experiences';

    protected $fillable = [
        'company_name',
        'location',
        'job_position',
        'from_date',
        'to_date',
        'user_id'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
