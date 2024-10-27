<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Policy extends Model {
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'policy'
    ];

    public function departments()
    {
        return $this->belongsToMany(Department::class)->withPivot('start_time', 'status');
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('start_time', 'status');
    }

    public function pay_roll_settings()
    {
        return $this->hasOne(PolicyPayRollSetting::class);
    }

    public function working_settings()
    {
        return $this->hasOne(PolicyWorkingSetting::class);
    }

    public function working_day()
    {
        return $this->hasMany(PolicyWorkingDay::class);
    }

    public function overtime()
    {
        return $this->hasOne(PolicyOvertime::class);
    }

    public function holiday_overtime()
    {
        return $this->hasOne(PolicyHolidayOvertime::class);
    }
}