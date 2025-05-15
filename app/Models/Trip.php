<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    protected $table = 'trips';

    protected $fillable = [
        'name',
        'contact_email',
        'description',
        'start_date',
        'end_date',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];
    
    /**
     * Get all travellers for this trip
     */
    public function travellers()
    {
        return $this->hasMany(Traveller::class);
    }
    
    /**
     * Get all groups for this trip
     */
    public function groups()
    {
        return $this->hasMany(Group::class);
    }
    
    /**
     * Get remaining places on this trip
     */
    public function getRemainingPlaces()
    {
        // Can be implemented if there's a max_travellers field
        return null;
    }
    
    /**
     * Get duration in days
     */
    public function getDurationInDays()
    {
        if ($this->start_date && $this->end_date) {
            return $this->start_date->diffInDays($this->end_date) + 1;
        }
        return null;
    }
}
