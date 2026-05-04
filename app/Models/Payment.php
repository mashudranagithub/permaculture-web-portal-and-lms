<?php

namespace App\Models;

use App\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasOrganization, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'user_id',
        'batch_id',
        'enrollment_id',
        'amount',
        'discount_applied',
        'net_amount',
        'currency',
        'gateway',
        'transaction_id',
        'status',
        'payment_date',
        'payment_details',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_details' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }
}
