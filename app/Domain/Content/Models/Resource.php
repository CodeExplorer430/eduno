<?php

declare(strict_types=1);

namespace App\Domain\Content\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read Lesson $lesson
 */
class Resource extends Model
{
    protected $fillable = [
        'lesson_id',
        'title',
        'file_path',
        'mime_type',
        'size_bytes',
        'visibility',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
