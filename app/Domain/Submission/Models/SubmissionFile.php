<?php

declare(strict_types=1);

namespace App\Domain\Submission\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $submission_id
 * @property string $file_path
 * @property string $original_name
 * @property string $mime_type
 * @property int $size_bytes
 */
class SubmissionFile extends Model
{
    protected $fillable = [
        'submission_id',
        'file_path',
        'original_name',
        'mime_type',
        'size_bytes',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }
}
