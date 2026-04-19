<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassSession extends Model
{
    protected $fillable = [
        'batch_id',
        'topic_id',
        'title',
        'scheduled_at',
        'duration_minutes',
        'meeting_link',
        'meeting_password',
        'platform',
        'status',
        'youtube_video_url',
        'video_uploaded_at',
        'instructor_name',
        'notes'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'video_uploaded_at' => 'datetime',
        'notes' => 'array',
        'duration_minutes' => 'integer'
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }
}
