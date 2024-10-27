<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeviceLog extends Model {
    use HasFactory;

    protected $table = 'device_logs';

    protected $fillable = [
        'device_id',
        'time',
        'user_id',
        'type',
        'date',
        'log_id',
        'imported'
    ];

    public function policy()
    {
        return $this->belongsTo(Policy::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dateable()
    {
        return $this->morphTo();
    }

    public function setDateAttribute($value)
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
        $this->attributes['date'] = $formattedUtcTime;
    }
}
