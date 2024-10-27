<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeavePlan extends Model {
    use HasFactory, SoftDeletes;
    protected $table = 'leave_plans';
    protected $fillable = [
        'title',
        'c_from_date',
        'c_to_date',
        'quantity',
        'unit_id',
        'carry_forward',
        'consective_leaves',
        'apply_after_year',
        'apply_after_month',
        'leave_type_id',
        'leave_gender_type',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function leave_type()
    {
        return $this->belongsTo(LeaveType::class);
    }
}