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
    ];

    protected function casts(): array
    {
        return [
            'visibility' => ResourceVisibility::class,
        ];
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
