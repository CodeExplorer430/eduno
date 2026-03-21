<?php

declare(strict_types=1);

namespace App\Domain\Content\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read Module $module
 * @property-read Collection<int, Resource> $resources
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
            'published_at' => 'datetime',
            'order_no' => 'integer',
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
}
