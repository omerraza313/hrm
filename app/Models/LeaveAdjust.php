<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveAdjust extends Model {
    use HasFactory;

    protected $table = 'leave_adjusts';

    protected $fillable = [
        'leave_date',
        'leave_plan_id',
        'applyleave_id',
        'quantity'
    ];

    public function leave_plan()
    {
        return $this->belongsTo(LeavePlan::class);
    }
}