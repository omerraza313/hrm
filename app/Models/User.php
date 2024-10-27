<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\Permissions\AssignPermissions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable {
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HasPermissions, AssignPermissions, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'image',
        'note',
        'clear_status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function employee_details()
    {
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
    public function assign_leaves(): mixed
    {
        return $this->hasMany(AssignLeave::class);
    }

    public function policy()
    {
        return $this->belongsToMany(Policy::class)->withPivot('start_time', 'status');
    }

    public function applyLeaves()
    {
        return $this->hasMany(Applyleaves::class);
    }

    public function salary()
    {
        return $this->hasMany(Salary::class);
    }

    public function deactive_user()
    {
        return $this->hasOne(DeactiveUser::class);
    }

    public function attendences()
    {
        return $this->hasMany(Attendence::class);
    }
}