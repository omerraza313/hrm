<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserDetail extends Model {
    use HasFactory;

    protected $table = 'user_details';

    protected $fillable = [
        'pseudo_name',
        'dob',
        'join_date',
        'phone',
        'martial_status',
        'cnic',
        'gender',
        'department_id',
        'designation_id',
        'user_id',
        'salary',
        'blood_group',
        'manager_id'
    ];


    public function department()
    {
        return $this->hasOne(Department::class, 'id', 'department_id');
    }
    public function designation()
    {
        return $this->hasOne(Designation::class, 'id', 'designation_id');
//        return $this->belongsTo(Designation::class, 'id', 'designation_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function manager()
    {
        return $this->hasOne(User::class, 'id', 'manager_id');
    }
    public function setDobAttribute($value)
    {
        // dd($value);
        if (!strpos($value, "-")) {
            $utcTime = Carbon::createFromFormat('d/m/Y', $value, 'America/New_York');
            $formattedUtcTime = $utcTime->format('Y-m-d');
        } else {
            $formattedUtcTime = $value;
        }
        // dd($formattedUtcTime);
        $this->attributes['dob'] = $formattedUtcTime;
    }
    public function setJoinDateAttribute($value)
    {
        if (!strpos($value, "-")) {
            $utcTime = Carbon::createFromFormat('d/m/Y', $value, 'America/New_York');
            $formattedUtcTime = $utcTime->format('Y-m-d');
        } else {
            $formattedUtcTime = $value;
        }

        // dd($formattedUtcTime);
        $this->attributes['join_date'] = $formattedUtcTime;
    }

    // public function getDobAttribute($value)
    // {
    //     // dd($value);
    //     // dd(Carbon::parse($value, 'utc')->setTimezone('America/New_York')->format('Y-m-d'));
    //     return Carbon::parse($value, 'utc')->setTimezone('America/New_York')->format('Y-m-d');
    // }

    public function getJoinDateAttribute($value)
    {
        // return Carbon::parse($value, 'utc')->setTimezone('America/New_York')->format('d/m/Y');
        return Carbon::parse($value, 'utc')->format('d/m/Y');
    }
    public function getDobAttribute($value)
    {
        // return Carbon::parse($value, 'utc')->setTimezone('America/New_York')->format('d/m/Y');
        return Carbon::parse($value, 'utc')->format('d/m/Y');
    }
}
