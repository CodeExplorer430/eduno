<?php

declare(strict_types=1);

namespace App\Domain\Announcement\Models;

use App\Domain\Course\Models\CourseSection;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read CourseSection $courseSection
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

    public function courseSection(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
