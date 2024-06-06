<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded= [];
    public function park()
    {
        return $this->belongsTo(RegionKey::class, 'park_id');
    }

    public function DriverLicenseCountry()
    {
        return $this->belongsTo(DriverLicense::class, 'driver_license_country_id');
    }

    public function car()
    {
        return $this->hasMany(UserCar::class, 'user_id')->orderBy('id', 'desc')->limit(1);
    }

    public function jobs()
    {
        return $this->hasMany(Jobs::class, 'user_id')->orderBy('created_at', 'asc')->where('status', 'complete');
    }
    public function WorkRule()
    {
        return $this->belongsto(YandexWorkRule::class, 'work_rule_id');
    }
    public function notifications()
    {
        return $this->hasMany(UserNotificationPivot::class, 'user_id');
    }
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
    ];
}
