<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TopicAttachment extends Model
{
    protected $fillable = [
        'topic_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size'
    ];

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }
}
