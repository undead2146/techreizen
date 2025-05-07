<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Traveller extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'trip_id',
        'zip_id',
        'group_id',
        'major_id',
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
        'remember_token',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'birth_date' => 'date',
    ];

    /**
     * Get the user that owns the traveller profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the trip that the traveller is registered for.
     */
    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    /**
     * Get the education that the traveller is in.
     */
    public function education()
    {
        return $this->belongsTo(Education::class);
    }

    /**
     * Get the major that the traveller is in.
     */
    public function major()
    {
        return $this->belongsTo(Major::class);
    }

    /**
     * Get the group that the traveller belongs to.
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get the groups the traveller is a member of through group_members
     */
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_members', 'traveller_id', 'group_id')
                    ->withTimestamps()
                    ->withPivot('joined_at');
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
            
            // Also add to the group_members pivot table
            if (!$this->groups->contains($group->id)) {
                $this->groups()->attach($group->id, ['joined_at' => now()]);
            }
            
            return $this->save();
        }
        return false;
    }
    
    /**
     * Leave current group
     */
    public function leaveGroup()
    {
        if ($this->group_id) {
            $groupId = $this->group_id;
            
            // Remove from pivot table
            $this->groups()->detach($groupId);
            
            // Remove direct group reference
            $this->group_id = null;
            return $this->save();
        }
        return true; // Already not in a group
    }
}
