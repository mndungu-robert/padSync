<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReceiptConfirmation extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'confirmation_id';

    protected $fillable = [
        'distribution_id',
        'coordinator_id',
        'received_quantity',
        'confirmation_date',
    ];

    protected $casts = [
        'confirmation_date' => 'datetime',
    ];

    public function distribution(): BelongsTo
    {
        return $this->belongsTo(Distribution::class, 'distribution_id', 'distribution_id');
    }

    public function coordinator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coordinator_id');
    }
}
