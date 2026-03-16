<?php

declare(strict_types=1);

namespace App\Domain\Grade\Models;

use App\Domain\Submission\Models\Submission;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grade extends Model
{
    protected $fillable = [
        'submission_id',
        'graded_by',
        'score',
        'feedback',
        'released_at',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'released_at' => 'datetime',
        ];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by', 'id');
    }
}
