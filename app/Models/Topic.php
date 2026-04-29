<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Topic extends Model
{
    use HasTranslations, SoftDeletes;

    protected $fillable = [
        'course_id',
        'title',
        'content_body',
        'description',
        'order_index',
        'topic_type',
        'pdf_file_en',
        'pdf_file_bn',
        'video_url',
        'audio_file_en',
        'audio_file_bn',
        'estimated_duration',
        'is_published',
        'is_free_preview'
    ];

    protected $casts = [
        'title' => 'array',
        'content_body' => 'array',
        'description' => 'array',
        'is_published' => 'boolean',
        'is_free_preview' => 'boolean',
        'order_index' => 'integer',
        'topic_type' => 'string'
    ];

    /**
     * Get the course that owns the topic.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the attachments for the topic.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(TopicAttachment::class);
    }

    /**
     * Get the live sessions linked to this topic.
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(ClassSession::class);
    }

    /**
     * Get the student progress records for this topic.
     */
    public function progress(): HasMany
    {
        return $this->hasMany(TopicProgress::class);
    }
}
