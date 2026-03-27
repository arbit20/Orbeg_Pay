<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DocumentType;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use Auditable, HasFactory, SoftDeletes;

    protected $fillable = [
        'document_type',
        'document_number',
        'business_name',
        'trade_name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'contact_person',
        'contact_phone',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'document_type' => DocumentType::class,
            'is_active' => 'boolean',
        ];
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
