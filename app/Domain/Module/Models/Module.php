<?php

declare(strict_types=1);

namespace App\Domain\Module\Models;

use App\Domain\Course\Models\CourseSection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $course_section_id
 * @property string $title
 * @property string|null $description
 * @property int $order_no
 * @property Carbon|null $published_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read CourseSection $section
 * @property-read Collection<int, Lesson> $lessons
 */
class Module extends Model
{
    protected $fillable = [
        'course_section_id',
        'title',
        'description',
        'order_no',
        'published_at',
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

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null && $this->published_at->isPast();
    }
}
