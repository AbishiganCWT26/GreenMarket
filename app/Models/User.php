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
    | Boot / Model Events
    |--------------------------------------------------------------------------
    */

    protected static function boot()
    {
        parent::boot();

        static::updated(function ($user) {
            if (auth()->check() && $user->wasChanged(['username', 'email', 'profile_photo'])) {
                switch ($user->role) {
                    case 'farmer':
                        if ($user->farmer) {
                            $user->farmer->update(['updated_by' => auth()->id()]);
                        }
                        break;
                    case 'lead_farmer':
                        if ($user->leadFarmer) {
                            $user->leadFarmer->update(['updated_by' => auth()->id()]);
                        }
                        break;
                    case 'buyer':
                        if ($user->buyer) {
                            $user->buyer->update(['updated_by' => auth()->id()]);
                        }
                        break;
                    case 'facilitator':
                        if ($user->facilitator) {
                            $user->facilitator->update(['updated_by' => auth()->id()]);
                        }
                        break;
                    case 'admin':
                        if ($user->adminDetails) {
                            $user->adminDetails->update(['updated_by' => auth()->id()]);
                        }
                        break;
                }
            }
        });
    }

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

	public function adminDetails()
	{
		return $this->hasOne(Admin::class, 'user_id');
	}

	public function deliveryRider()
	{
		return $this->hasOne(DeliveryRider::class, 'user_id');
	}

	public function riderDeliveries()
	{
		return $this->hasMany(RiderDelivery::class, 'rider_id');
	}

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }
}
