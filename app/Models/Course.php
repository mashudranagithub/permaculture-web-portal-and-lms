<?php

namespace App\Models;

use App\Traits\HasTranslations;
use App\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasTranslations, SoftDeletes, HasOrganization;

    protected $fillable = [
        'organization_id',
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

    protected $appends = [
        'image_url',
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
     * Get the course's image URL.
     */
    public function getImageUrlAttribute(): string
    {
        if (!$this->image) {
            return 'https://images.unsplash.com/photo-1592419044706-39796d40f98c?auto=format&fit=crop&q=80&w=800';
        }

        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        return asset('storage/' . $this->image);
    }

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

    /**
     * Scope to filter active/published courses.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'published')->where('is_active', true);
    }
}
