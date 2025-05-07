<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Traveller extends Model
{
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'travellers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'trip_id',
        'zip_id',
        'major_id',
        'group_id',
        'first_name',
        'last_name',
        'email',
        'country',
        'address',
        'gender',
        'phone',
        'emergency_phone_1',
        'emergency_phone_2',
        'nationality',
        'birthdate',
        'birthplace',
        'iban',
        'bic',
        'medical_issue',
        'medical_info',
    ];

    /**
     * Default values for attributes
     *
     * @var array
     */
    protected $attributes = [
        'iban' => 'BE00000000000000',
        'bic' => 'GEBABEBB',
        'medical_issue' => 0,
        'medical_info' => '',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'birthdate' => 'date',
            'medical_issue' => 'boolean',
        ];
    }
    
    /**
     * Get the user associated with this traveller
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the trip this traveller belongs to
     */
    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }
    
    /**
     * Get the groups this traveller is a member of
     */
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_members', 'traveller_id', 'group_id')->withTimestamps();
    }
    
    /**
     * Get the group this traveller belongs to
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }
    
    /**
     * Check if traveller can manage groups (has a user with guide or admin role)
     */
    public function canManageGroups()
    {
        return $this->user && in_array($this->user->role, ['guide', 'admin']);
    }
    
    /**
     * Check if traveller is in any group
     */
    public function hasGroup()
    {
        return !is_null($this->group_id);
    }
    
    /**
     * Join a group
     */
    public function joinGroup(Group $group)
    {
        if (!$group->isFull()) {
            $this->group_id = $group->id;
            return $this->save();
        }
        return false;
    }
    
    /**
     * Leave current group
     */
    public function leaveGroup()
    {
        $this->group_id = null;
        return $this->save();
    }
}
