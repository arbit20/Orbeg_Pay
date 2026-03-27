<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MetalPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'metal_id',
        'price_per_gram',
        'price_per_troy_ounce',
        'currency',
        'effective_date',
        'source',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'price_per_gram' => 'decimal:4',
            'price_per_troy_ounce' => 'decimal:4',
            'effective_date' => 'date',
        ];
    }

    public function metal(): BelongsTo
    {
        return $this->belongsTo(Metal::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
