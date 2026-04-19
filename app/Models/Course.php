<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasTranslations, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'short_description',
        'image',
        'banner_image',
        'price',
        'level',
        'delivery_mode',
        'duration',
        'status',
        'is_featured',
        'created_by',
        'meta_title',
        'meta_description',
        'is_active'
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'short_description' => 'array',
        'meta_title' => 'array',
        'meta_description' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'delivery_mode' => 'string',
        'status' => 'string'
    ];

    /**
     * Get the batches for the course.
     */
    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class);
    }

    /**
     * Get the topics for the course.
     */
    public function topics(): HasMany
    {
        return $this->hasMany(Topic::class)->orderBy('order_index');
    }

    /**
     * Get the user who created the course.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Helper to get active batches.
     */
    public function activeBatches()
    {
        return $this->batches()->where('is_enrollment_open', true)->where('status', 'upcoming');
    }
}
