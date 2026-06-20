<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShortfallReport extends Model
{
    protected $primaryKey = 'report_id';

    protected $fillable = [
        'school_id',
        'report_date',
        'required_pads',
        'available_pads',
        'shortfall',
        'status',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id', 'school_id');
    }
}
