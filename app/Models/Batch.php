<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property \Carbon\Carbon $start_date
 * @property \Carbon\Carbon $end_date
 * @property \Carbon\Carbon|null $enrollment_deadline
 */
class Batch extends Model
{
    use HasTranslations, SoftDeletes;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'start_date',
        'end_date',
        'enrollment_deadline',
        'capacity',
        'price',
        'discount_amount',
        'discount_type',
        'status',
        'is_enrollment_open'
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'enrollment_deadline' => 'date',
        'price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'is_enrollment_open' => 'boolean',
        'status' => 'string'
    ];

    /**
     * Get the course that owns the batch.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the enrollments for the batch.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get the payments related to the batch.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the live sessions for the batch.
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(ClassSession::class);
    }

    /**
     * Get the weekly routine for the batch.
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(BatchSchedule::class);
    }

    /**
     * Helper to get available seats.
     */
    public function getAvailableSeatsAttribute()
    {
        if ($this->capacity === 0) return 9999;
        return $this->capacity - $this->enrollments()->where('status', 'active')->count();
    }
}
