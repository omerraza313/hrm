<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DateLog extends Model {
    use HasFactory;

    protected $table = 'dates';

    protected $fillable = [
        'user_id',
        'date',
        'dateable_type',
        'dateable_id',
        'type'
    ];

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