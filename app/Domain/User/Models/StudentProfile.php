<?php

declare(strict_types=1);

namespace App\Domain\User\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentProfile extends Model
{
    protected $fillable = [
        'user_id',
        'student_number',
        'program',
        'year_level',
        'section',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
