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
        'max_members',
        'locked'
    ];

    protected $casts = [
        'locked' => 'boolean',
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
     * Get all travelers in this group through the direct relationship
     */
    public function members()
    {
        return $this->hasMany(Traveller::class, 'group_id');
    }

    /**
     * Check if group is full
     */
    public function isFull()
    {
        return $this->members()->count() >= $this->max_members;
    }

    /**
     * Get the current member count
     */
    public function getMemberCount()
    {
        return $this->members()->count();
    }

    /**
     * Check if the group is locked
     */
    public function isLocked(): bool
    {
        return (bool)$this->locked;
    }
    
    /**
     * Toggle the locked status of the group
     */
    public function toggleLock(): bool
    {
        $this->locked = !$this->locked;
        return $this->save();
    }
}
