<?php

declare(strict_types=1);

namespace App\Domain\Accessibility\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreference extends Model
{
    protected $fillable = [
        'user_id',
        'font_size',
        'high_contrast',
        'reduced_motion',
        'simplified_layout',
        'language',
    ];

    protected function casts(): array
    {
        return [
            'high_contrast' => 'boolean',
            'reduced_motion' => 'boolean',
            'simplified_layout' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
