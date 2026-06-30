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
        'amount_kes',
        'payment_method',
        'payment_status',
        'payment_reference',
        'merchant_request_id',
        'checkout_request_id',
        'payer_phone',
        'paid_at',
        'callback_payload',
        'expected_delivery_date',
        'fulfillment_date',
        'notes',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'callback_payload' => 'array',
    ];

    public function donor()
    {
        return $this->belongsTo(Donor::class, 'donor_id');
    }
}
