<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ShipmentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'shippable_type',
        'shippable_id',
        'user_id',
        'tracking_number',
        'carrier',
        'origin_address',
        'destination_address',
        'shipped_date',
        'estimated_delivery',
        'actual_delivery',
        'status',
        'total_weight_grams',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => ShipmentStatus::class,
            'shipped_date' => 'date',
            'estimated_delivery' => 'date',
            'actual_delivery' => 'date',
            'total_weight_grams' => 'decimal:4',
        ];
    }

    public function shippable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
