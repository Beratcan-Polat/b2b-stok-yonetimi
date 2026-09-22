<?php

namespace App\Policies;

use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('siparis.listele');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('siparis.olustur');
    }
}
