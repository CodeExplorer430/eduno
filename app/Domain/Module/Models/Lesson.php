<?php

declare(strict_types=1);

namespace App\Domain\Module\Models;

use App\Enums\LessonType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $module_id
 * @property string $title
 * @property string|null $content
 * @property LessonType $type
 * @property int $order_no
 * @property Carbon|null $published_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Module $module
 * @property-read Collection<int, \App\Domain\Module\Models\Resource> $resources
 */
class Lesson extends Model
{
    protected $fillable = [
        'module_id',
        'title',
        'content',
        'type',
        'order_no',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => LessonType::class,
            'published_at' => 'datetime',
        ];
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class);
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null && $this->published_at->isPast();
    }
}
