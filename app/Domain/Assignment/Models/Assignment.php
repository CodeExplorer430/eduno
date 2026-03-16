<?php

declare(strict_types=1);

namespace App\Domain\Assignment\Models;

use App\Domain\Course\Models\CourseSection;
use App\Domain\Submission\Models\Submission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
            'max_score' => 'decimal:2',
            'allow_resubmission' => 'boolean',
        ];
    }

    public function courseSection(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }
}
