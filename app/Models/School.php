<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class School extends Model
{
    // Inform Laravel of your custom primary key
    protected $primaryKey = 'school_id';

    protected $fillable = [
        'school_name',
        'school_location',
        'enrollment',
    ];

    /**
     * Get all users (coordinators) assigned to this school.
     */
    public function coordinators(): HasMany
    {
        return $this->hasMany(User::class, 'school_id', 'school_id');
    }
}
