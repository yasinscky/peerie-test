<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
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
        'is_admin' => 'boolean',
    ];

    /**
     * Get user plans
     */
    public function plans()
    {
        return $this->hasMany(Plan::class);
    }

    /**
     * Last created user plan
     */
    public function latestPlan()
    {
        return $this->hasOne(Plan::class)->latestOfMany();
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        // Explicit cast to boolean for reliability
        return (bool) $this->is_admin;
    }
}
