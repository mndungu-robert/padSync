<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donor extends Model
{
    use HasFactory;

    //
    protected $fillable = [
        'name',
        'email',
        'pad_count',
        'donor_type',
        'organization_name',
    ];

    public function donations()
    {
        return $this->hasMany(Donation::class, 'donor_id');
    }
}
