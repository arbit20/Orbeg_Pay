<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SaleStatus;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Sale extends Model
{
    use Auditable, HasFactory;

    protected $fillable = [
        'sale_number',
        'client_id',
        'user_id',
        'sale_date',
        'sale_datetime',
        'lot_count',
        'weight_grams',
        'purity',
        'unit_price_bs',
        'total_bs',
        'reference_quote_usd_oz',
        'quote_source',
        'exchange_rate_bs_usd',
        'evidence_path',
        'status',
        'subtotal',
        'tax_percentage',
        'tax_amount',
        'total',
        'currency',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => SaleStatus::class,
            'sale_date' => 'date',
            'sale_datetime' => 'datetime',
            'lot_count' => 'integer',
            'weight_grams' => 'decimal:4',
            'purity' => 'decimal:4',
            'unit_price_bs' => 'decimal:4',
            'total_bs' => 'decimal:4',
            'reference_quote_usd_oz' => 'decimal:4',
            'exchange_rate_bs_usd' => 'decimal:4',
            'subtotal' => 'decimal:4',
            'tax_percentage' => 'decimal:2',
            'tax_amount' => 'decimal:4',
            'total' => 'decimal:4',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function settlements(): MorphMany
    {
        return $this->morphMany(Settlement::class, 'settleable');
    }

    public function shipments(): MorphMany
    {
        return $this->morphMany(Shipment::class, 'shippable');
    }

    /**
     * Recalcula los totales de la venta a partir de sus items.
     */
    public function recalculateTotals(): void
    {
        $subtotal = $this->items()->sum('subtotal');
        $taxAmount = bcmul((string) $subtotal, bcdiv((string) $this->tax_percentage, '100', 10), 4);

        $this->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total' => bcadd((string) $subtotal, $taxAmount, 4),
        ]);
    }
}
