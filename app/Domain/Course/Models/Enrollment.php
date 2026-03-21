<?php

declare(strict_types=1);

namespace App\Domain\Course\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int $course_section_id
 * @property string $status
 * @property Carbon $enrolled_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read User $student
 */
class Enrollment extends Model
{
    protected $fillable = [
        'user_id',
        'course_section_id',
        'status',
        'enrolled_at',
    ];

    protected function casts(): array
    {
        return [
            'enrolled_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'course_section_id');
    }
}
