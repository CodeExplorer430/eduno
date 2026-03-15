<?php

declare(strict_types=1);

namespace App\Domain\Submission\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $submission_id
 * @property int $graded_by
 * @property float $score
 * @property string|null $feedback
 * @property Carbon|null $released_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Submission $submission
 * @property-read User $gradedBy
 */
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
            'released_at' => 'datetime',
            'score' => 'decimal:2',
        ];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function gradedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    public function isReleased(): bool
    {
        return $this->released_at !== null;
    }
}
