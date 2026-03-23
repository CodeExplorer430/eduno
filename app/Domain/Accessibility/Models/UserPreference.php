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
        'dyslexia_font',
        'simplified_layout',
        'language',
        'email_notifications',
        'email_digest',
    ];

    protected function casts(): array
    {
        return [
            'high_contrast'       => 'boolean',
            'reduced_motion'      => 'boolean',
            'dyslexia_font'       => 'boolean',
            'simplified_layout'   => 'boolean',
            'email_notifications' => 'boolean',
            'email_digest'        => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
