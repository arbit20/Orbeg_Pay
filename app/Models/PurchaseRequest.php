<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PurchaseRequestStatus;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseRequest extends Model
{
    use Auditable, HasFactory;

    protected $fillable = [
        'request_number',
        'supplier_id',
        'user_id',
        'metal_id',
        'estimated_weight_grams',
        'estimated_purity',
        'quoted_price_per_gram',
        'estimated_total',
        'currency',
        'status',
        'purchase_id',
        'reviewed_by',
        'reviewed_at',
        'client_notes',
        'operator_notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => PurchaseRequestStatus::class,
            'estimated_weight_grams' => 'decimal:4',
            'estimated_purity' => 'decimal:4',
            'quoted_price_per_gram' => 'decimal:4',
            'estimated_total' => 'decimal:4',
            'reviewed_at' => 'datetime',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function metal(): BelongsTo
    {
        return $this->belongsTo(Metal::class);
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Genera el siguiente número de solicitud (SOL-XXXX).
     */
    public static function generateNextNumber(): string
    {
        $last = static::orderByDesc('id')->value('request_number');

        if ($last && preg_match('/SOL-(\d+)/', $last, $matches)) {
            $next = (int) $matches[1] + 1;
        } else {
            $next = 1;
        }

        return 'SOL-' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
