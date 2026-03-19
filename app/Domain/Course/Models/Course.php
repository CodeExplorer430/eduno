<?php

declare(strict_types=1);

namespace App\Domain\Course\Models;

use App\Enums\CourseStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $code
 * @property string $title
 * @property string|null $description
 * @property string $department
 * @property string $term
 * @property string $academic_year
 * @property CourseStatus $status
 * @property int $created_by
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Course extends Model
{
    protected $fillable = [
        'code',
        'title',
        'description',
        'department',
        'term',
        'academic_year',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => CourseStatus::class,
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(CourseSection::class);
    }
}
