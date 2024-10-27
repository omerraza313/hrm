<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignLeave extends Model
{
    use HasFactory;

    protected $table = 'assign_leaves';

    protected $fillable = [
        'leave_plan_id',
        'user_id',
        'remaining_leave',
    ];

    public function leave_plan(){
        return $this->belongsTo(LeavePlan::class);
    }
}
