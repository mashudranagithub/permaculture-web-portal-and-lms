<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BatchSchedule extends Model
{
    protected $fillable = [
        'batch_id',
        'day_of_week',
        'start_time',
        'end_time',
        'platform',
        'is_recurring'
    ];

    protected $casts = [
        'is_recurring' => 'boolean'
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }
}
