<?php

declare(strict_types=1);

namespace App\Domain\Submission\Models;

use App\Domain\Assignment\Models\Assignment;
use App\Domain\Grade\Models\Grade;
use App\Models\User;
use App\Support\Enums\SubmissionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Submission extends Model
{
    protected $fillable = [
        'assignment_id',
        'student_id',
        'status',
        'submitted_at',
        'is_late',
        'attempt_no',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'is_late' => 'boolean',
            'status' => SubmissionStatus::class,
        ];
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id', 'id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(SubmissionFile::class);
    }

    public function grade(): HasOne
    {
        return $this->hasOne(Grade::class);
    }
}
