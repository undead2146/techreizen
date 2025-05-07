<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Auth\CanResetPassword;

class User extends Authenticatable implements CanResetPassword
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'login',
        'password',
        'role',
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

    /**
     * Get the traveller associated with the user.
     */
    public function traveller()
    {
        return $this->hasOne(Traveller::class);
    }

    /**
     * Get groups created by this user
     */
    public function createdGroups()
    {
        return $this->hasMany(Group::class, 'created_by');
    }

    /**
     * Get the group associated with this user's traveller profile
     */
    public function getGroup()
    {
        return $this->traveller ? $this->traveller->group : null;
    }

    /**
     * Check if user can manage groups (is a guide or admin)
     */
    public function canManageGroups()
    {
        return in_array($this->role, ['guide', 'admin']);
    }

    /**
     * Get the trip associated with this user through traveller
     */
    public function getTrip()
    {
        return $this->traveller ? $this->traveller->trip : null;
    }

    /**
     * Get trip ID associated with this user
     */
    public function getTripId()
    {
        return $this->traveller ? $this->traveller->trip_id : null;
    }

    /**
     * Get the email address used for password resets.
     *
     * @return string
     */
    public function getEmailForPasswordReset()
    {
        $traveller = $this->traveller()->first();
        return $traveller ? $traveller->email : null;
    }

    /**
     * Route notifications for the mail channel.
     *
     * @return string
     */
    public function routeNotificationForMail()
    {
        return $this->getEmailForPasswordReset();
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Explicitly set the password attribute with hashing.
     *
     * @param  string  $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        // Only hash the password if it's not already hashed
        if ($value && !preg_match('/^\$2y\$/', $value)) {
            $this->attributes['password'] = Hash::make($value);
        } else {
            $this->attributes['password'] = $value;
        }
    }
}
