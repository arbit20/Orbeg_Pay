<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class SaleItem extends Model
{
    use HasFactory;

    public const TROY_OUNCE_IN_GRAMS = '31.1035';

    protected $fillable = [
        'sale_id',
        'metal_id',
        'description',
        'weight_grams',
        'purity',
        'price_per_gram',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'weight_grams' => 'decimal:4',
            'purity' => 'decimal:4',
            'price_per_gram' => 'decimal:4',
            'subtotal' => 'decimal:4',
        ];
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function metal(): BelongsTo
    {
        return $this->belongsTo(Metal::class);
    }

    public function inventoryMovements(): MorphMany
    {
        return $this->morphMany(InventoryMovement::class, 'movementable');
    }

    public function getWeightTroyOuncesAttribute(): string
    {
        return bcdiv((string) $this->weight_grams, self::TROY_OUNCE_IN_GRAMS, 4);
    }

    public function calculateSubtotal(): string
    {
        $fineWeight = bcmul((string) $this->weight_grams, (string) $this->purity, 10);
        return bcmul($fineWeight, (string) $this->price_per_gram, 4);
    }
}
