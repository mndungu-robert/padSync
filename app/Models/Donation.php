<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    // Define the columns to match your migration names
    protected $primaryKey = 'donation_id';

    protected $fillable = [
        'donor_id',
        'pad_count',
        'pledge_date',
        'expected_delivery_date',
        'fulfillment_date',
        'notes',
    ];

    public function donor()
    {
        return $this->belongsTo(Donor::class, 'donor_id');
    }
}
