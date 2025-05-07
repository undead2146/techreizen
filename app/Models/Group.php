<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Group extends Model
{
    use HasFactory;

    protected $table = 'groups'; // <-- Zorg ervoor dat dit er staat!

    protected $fillable = [
        'name',
        'max_members',
    ];
}
