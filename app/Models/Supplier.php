<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DocumentType;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use Auditable, HasFactory, SoftDeletes;

    protected $fillable = [
        'supplier_type',
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
        'bank_name',
        'bank_account_number',
        'bank_cci',
        'payment_method',
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

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function purchaseRequests(): HasMany
    {
        return $this->hasMany(PurchaseRequest::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }
}
