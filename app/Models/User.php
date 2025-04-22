<?php

namespace App\Models;

use App\Models\Traveller;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable implements CanResetPassword
{
    use  HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'login',
        'name',
        'email',
        'password',
        'role',
        'first_name',
        'last_name',
        // Additional fields
        'phone',
        'emergency_contact',
        'optional_emergency_contact',
        'medical_info',
        'medical_details',
        'gender',
        'nationality',
        'date_of_birth',
        'place_of_birth',
        'address',
        'city',
        'country',
        'trip',
        'student_number',
        'education',
        'major',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the traveller associated with the user.
     */
    public function traveller()
    {
        return $this->hasOne(Traveller::class);
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
