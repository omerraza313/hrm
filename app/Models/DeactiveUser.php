<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeactiveUser extends Model {
    use HasFactory;

    protected $table = 'deactive_users';

    protected $fillable = [
        'user_id',
        'notice_period_served',
        'notice_period_date',
        'notice_period_duration',
        'exit_date',
        'all_cleared',
        'reason',
        'comments',
    ];

    public function setNoticePeriodDateAttribute($value)
    {
        // dd($value);
        if (!strpos($value, "-")) {
            $utcTime = Carbon::createFromFormat('d/m/Y', $value)
                ->setTimezone('UTC');
            $formattedUtcTime = $utcTime->format('Y-m-d');
        } else {
            $formattedUtcTime = $value;
        }
        // dd($formattedUtcTime);
        $this->attributes['notice_period_date'] = $formattedUtcTime;
    }

    public function setExitDateAttribute($value)
    {
        // dd($value);
        if (!strpos($value, "-")) {
            $utcTime = Carbon::createFromFormat('d/m/Y', $value)
                ->setTimezone('UTC');
            $formattedUtcTime = $utcTime->format('Y-m-d');
        } else {
            $formattedUtcTime = $value;
        }
        // dd($formattedUtcTime);
        $this->attributes['exit_date'] = $formattedUtcTime;
    }
}
