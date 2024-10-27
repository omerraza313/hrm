<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendence extends Model {
    use HasFactory;

    protected $fillable = [
        'user_id',
        'arrival_time',
        'leave_time',
        'arrival_date',
        'leave_date',
        'policy_id',
        'status',
        'remarks',
        'device_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function policy()
    {
        return $this->belongsTo(Policy::class);
    }

    public function setArrivalTimeAttribute($value)
    {
        $utcTime = Carbon::parse($value, 'America/New_York')->utc();

        $this->attributes['arrival_time'] = $utcTime;
    }
    public function setLeaveTimeAttribute($value)
    {
        $utcTime = Carbon::parse($value, 'America/New_York')->utc();

        $this->attributes['leave_time'] = $utcTime;
    }

    public function getArrivalTimeAttribute($value)
    {
        $localTime = Carbon::parse($value);
        // dd($localTime);
        return $localTime;
    }

    public function getLeaveTimeAttribute($value)
    {
        $localTime = Carbon::parse($value);
        return $localTime;
    }
}
