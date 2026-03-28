<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\MetalPrice;
use App\Models\User;

class MetalPricePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('metal_prices.view');
    }

    public function create(User $user): bool
    {
        return $user->can('metal_prices.create');
    }
}
