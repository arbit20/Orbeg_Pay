<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Metal;
use App\Models\User;

class MetalPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('metals.view');
    }

    public function view(User $user, Metal $metal): bool
    {
        return $user->can('metals.view');
    }

    public function create(User $user): bool
    {
        return $user->can('metals.create');
    }

    public function update(User $user, Metal $metal): bool
    {
        return $user->can('metals.edit');
    }

    public function delete(User $user, Metal $metal): bool
    {
        return $user->can('metals.delete');
    }
}
