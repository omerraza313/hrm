<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $table = "users";
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
    ];

    public function employee_details(){
        return $this->hasOne(UserDetail::class);
    }

    public function emergency_contacts()
    {
        return $this->hasOne(FamilyContact::class)->where('ice_status', 1);
    }

    public function address()
    {
        return $this->hasMany(Address::class);
    }

    public function family_contacts()
    {
        return $this->hasMany(FamilyContact::class)->where('ice_status', 0);
    }

    public function bank()
    {
        return $this->hasOne(BankInformation::class);
    }
    public function educations(): array|object
    {
        return $this->hasMany(Education::class);
    }
    public function experiences(): array|object
    {
        return $this->hasMany(Experience::class);
    }
}
