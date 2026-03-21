<?php

declare(strict_types=1);

namespace App\Domain\Assignment\Models;

use App\Domain\Course\Models\CourseSection;
use App\Domain\Submission\Models\Submission;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $course_section_id
 * @property string $title
 * @property string|null $instructions
 * @property Carbon|null $due_at
 * @property float $max_score
 * @property bool $allow_resubmission
 * @property Carbon|null $published_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read CourseSection $section
 * @property-read Collection<int, Submission> $submissions
 */
class Assignment extends Model
{
    protected $fillable = [
        'course_section_id',
        'title',
        'instructions',
        'due_at',
        'max_score',
        'allow_resubmission',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'due_at' => 'datetime',
            'published_at' => 'datetime',
            'allow_resubmission' => 'boolean',
            'max_score' => 'decimal:2',
        ];
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'course_section_id');
    }

    public function courseSection(): BelongsTo
    {
        return $this->section();
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null && $this->published_at->isPast();
    }

    public function isPastDue(): bool
    {
        return $this->due_at !== null && $this->due_at->isPast();
    }
}
