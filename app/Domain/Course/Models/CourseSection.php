<?php

declare(strict_types=1);

namespace App\Domain\Course\Models;

use App\Domain\Module\Models\Module;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $course_id
 * @property string $section_name
 * @property int $instructor_id
 * @property string|null $schedule_text
 * @property Carbon $created_at
 * @property Carbon $updated_at
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
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function modules(): HasMany
    {
        return $this->hasMany(Module::class);
    }
}
