<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PolicyWorkingDay extends Model {
    use HasFactory;
    protected $fillable = [
        'policy_id',
        'day',
        'start_time',
        'close_time',
        'active',
    ];

    public function setStartTimeAttribute($value)
    {
        $utcTime = Carbon::parse($value, 'America/New_York')->utc();
        $formattedUtcTime = $utcTime->format('h:i A');
        $this->attributes['start_time'] = $formattedUtcTime;
    }
    public function setCloseTimeAttribute($value)
    {
        $utcTime = Carbon::parse($value, 'America/New_York')->utc();
        $formattedUtcTime = $utcTime->format('h:i A');
        $this->attributes['close_time'] = $formattedUtcTime;
    }

    public function getStartTimeAttribute($value)
    {
        $local_ime = Carbon::parse($value)->timezone('America/New_York');
        return $local_ime->format('h:i A');
    }
    public function getCloseTimeAttribute($value)
    {
        $localTime = Carbon::parse($value)->timezone('America/New_York');
        return $localTime->format('h:i A');
    }
}