<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
        use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'avatar',
        'timezone',
        'language',
        'status',
        'preferences'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'preferences' => 'array',
    ];
    public function isAdmin(): bool
    {
        return strtolower($this->role) === 'admin';
    }
    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public function environments()
    {
        return $this->hasMany(Environment::class);
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }
    public function activeEnergyTariff()
    {
        return $this->hasOne(EnergyTariff::class)->where('is_active', true);
    }
    public function alertRules()
    {
        return $this->hasMany(AlertRule::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}
