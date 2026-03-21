<?php

declare(strict_types=1);

namespace App\Domain\Announcement\Models;

use App\Domain\Course\Models\CourseSection;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $course_section_id
 * @property string $title
 * @property string $body
 * @property Carbon|null $published_at
 * @property int $created_by
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read CourseSection|null $section
 * @property-read User $author
 */
class Announcement extends Model
{
    protected $fillable = [
        'course_section_id',
        'title',
        'body',
        'published_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'course_section_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null && $this->published_at->isPast();
    }
}
