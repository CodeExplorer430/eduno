<?php

declare(strict_types=1);

namespace App\Domain\Audit\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int         $id
 * @property int|null    $actor_id
 * @property string      $action
 * @property string|null $entity_type
 * @property int|null    $entity_id
 * @property array<string, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read User|null $actor
 */
class AuditLog extends Model
{
    public $timestamps = false;

    public const UPDATED_AT = null;

    protected $fillable = [
        'actor_id',
        'action',
        'entity_type',
        'entity_id',
        'metadata',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id', 'id');
    }
}
