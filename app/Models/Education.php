<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    use HasFactory;

    protected $table = "education";

    protected $fillable = [
        'name',
        'subject',
        'start_date',
        'complete_date',
        'degree',
        'grade',
        'user_id'
    ];

    public function employee(): array|object
    {
        return $this->belongsTo(Employee::class);
    }
}
