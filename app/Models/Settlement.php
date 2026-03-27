<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SettlementStatus;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Settlement extends Model
{
    use Auditable, HasFactory;

    protected $fillable = [
        'settlement_number',
        'settleable_type',
        'settleable_id',
        'user_id',
        'settlement_date',
        'total_amount',
        'currency',
        'status',
        'pdf_path',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => SettlementStatus::class,
            'settlement_date' => 'date',
            'total_amount' => 'decimal:4',
        ];
    }

    public function settleable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SettlementPayment::class);
    }

    /**
     * Monto total pagado contra esta liquidacion.
     */
    public function getTotalPaidAttribute(): string
    {
        return (string) $this->payments()->sum('amount');
    }

    /**
     * Saldo pendiente de pago.
     */
    public function getBalanceDueAttribute(): string
    {
        return bcsub((string) $this->total_amount, $this->total_paid, 4);
    }
}
