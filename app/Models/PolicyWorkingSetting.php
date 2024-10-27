<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PolicyWorkingSetting extends Model {
    use HasFactory;

    protected $fillable = [
        'policy_id',
        'shift_start',
        'shift_close',
        'late_c_l_t',
        'early_arrival_policy',
        'force_timeout',
        'timeout_policy',
        'late_minute_monthly_bucket',
        'late_comers_penalty',
    ];

    public function setShiftStartAttribute($value)
    {
        $utcTime = Carbon::parse($value, 'America/New_York')->utc();
        $formattedUtcTime = $utcTime->format('h:i A');
        // dd($formattedUtcTime);
        $this->attributes['shift_start'] = $formattedUtcTime;
    }

    public function setShiftCloseAttribute($value)
    {
        $utcTime = Carbon::parse($value, 'America/New_York')->utc();
        $formattedUtcTime = $utcTime->format('h:i A');
        $this->attributes['shift_close'] = $formattedUtcTime;
    }

    public function getShiftCloseAttribute($value)
    {
        $local_ime = Carbon::parse($value)->timezone('America/New_York');
        return $local_ime->format('h:i A');
    }
    public function getShiftStartAttribute($value)
    {
        $localTime = Carbon::parse($value)->timezone('America/New_York');
        return $localTime->format('h:i A');
    }
}