<?php

declare(strict_types=1);

namespace App\Domain\Module\Models;

use App\Enums\ResourceVisibility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $lesson_id
 * @property string $title
 * @property string $file_path
 * @property string $mime_type
 * @property int $size_bytes
 * @property ResourceVisibility $visibility
 * @property string|null $accessibility_notes
 * @property int|null $reading_time_minutes
 * @property Carbon $created_at
 * @property Carbon $updated_at
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
        'accessibility_notes',
        'reading_time_minutes',
    ];

    protected function casts(): array
    {
        return [
            'visibility'           => ResourceVisibility::class,
            'reading_time_minutes' => 'integer',
        ];
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
