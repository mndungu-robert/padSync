<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    // Define the columns to match your migration names
    protected $fillable = [
        'donor_id',
        'pad_count',
        'pledge_date',
        'pledge_status',
    ];
}
