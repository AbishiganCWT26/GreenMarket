<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     */
    protected $table = 'users';

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'id';

    /**
     * Attributes that are mass assignable.
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'role',
        'is_active',
        'profile_photo',
        'last_login',
    ];

    /**
     * Attributes hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casting for better data handling.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'last_login' => 'datetime',
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /*
    |--------------------------------------------------------------------------
    | Authentication Overrides
    |--------------------------------------------------------------------------
    */

    /**
     * Use 'username' for Passport/OAuth login.
     */
    public function findForPassport($username)
    {
        return $this->where('username', $username)->first();
    }

    /**
     * Fallback for Auth system to allow login via username OR email.
     */
    public function findForAuth($login)
    {
        return $this->where('username', $login)
                    ->orWhere('email', $login)
                    ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function farmer()
    {
        return $this->hasOne(Farmer::class, 'user_id');
    }

    public function leadFarmer()
    {
        return $this->hasOne(LeadFarmer::class, 'user_id');
    }

    public function buyer()
    {
        return $this->hasOne(Buyer::class, 'user_id');
    }

    public function facilitator()
    {
        return $this->hasOne(Facilitator::class, 'user_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }
}
