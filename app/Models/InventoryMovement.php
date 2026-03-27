<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MovementType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class InventoryMovement extends Model
{
    use HasFactory;

    public const TROY_OUNCE_IN_GRAMS = '31.1035';

    protected $fillable = [
        'metal_id',
        'movementable_type',
        'movementable_id',
        'type',
        'weight_grams',
        'purity',
        'reference',
        'notes',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'type' => MovementType::class,
            'weight_grams' => 'decimal:4',
            'purity' => 'decimal:4',
        ];
    }

    public function metal(): BelongsTo
    {
        return $this->belongsTo(Metal::class);
    }

    public function movementable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Peso fino (peso_gramos * pureza).
     */
    public function getFineWeightAttribute(): string
    {
        return bcmul((string) $this->weight_grams, (string) $this->purity, 4);
    }
}
