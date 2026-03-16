<?php

declare(strict_types=1);

namespace App\Domain\Course\Models;

use App\Models\User;
use App\Support\Enums\CourseStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function sections(): HasMany
    {
        return $this->hasMany(CourseSection::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
