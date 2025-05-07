<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'trip_id',
        'created_by',
        'max_members'
    ];

    /**
     * Get the trip that the group belongs to
     */
    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    /**
     * Get the creator of this group
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all travelers in this group
     */
    public function travellers()
    {
        return $this->hasMany(Traveller::class);
    }

    /**
     * Get the travellers (members) of this group through the group_members pivot
     */
    public function members()
    {
        return $this->belongsToMany(Traveller::class, 'group_members', 'group_id', 'traveller_id')
                    ->withTimestamps()
                    ->withPivot('joined_at');
    }
    
    /**
     * Check if group is full
     */
    public function isFull()
    {
        return $this->travellers()->count() >= $this->max_members;
    }
    
    /**
     * Get the current member count
     */
    public function getMemberCount()
    {
        return $this->travellers()->count();
    }
}
