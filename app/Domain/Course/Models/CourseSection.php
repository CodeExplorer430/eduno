<?php

declare(strict_types=1);

namespace App\Domain\Course\Models;

use App\Domain\Announcement\Models\Announcement;
use App\Domain\Assignment\Models\Assignment;
use App\Domain\Content\Models\Module;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $course_id
 * @property string $section_name
 * @property int $instructor_id
 * @property string|null $schedule_text
 */
class CourseSection extends Model
{
    protected $fillable = [
        'course_id',
        'section_name',
        'instructor_id',
        'schedule_text',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id', 'id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function modules(): HasMany
    {
        return $this->hasMany(Module::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }
}
