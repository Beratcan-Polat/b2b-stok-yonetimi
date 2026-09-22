<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('urun.listele');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('urun.ekle');
    }

    public function update(User $user, Product $urun): bool
    {
        return $user->hasPermissionTo('urun.duzenle');
    }

    public function delete(User $user, Product $urun): bool
    {
        return $user->hasPermissionTo('urun.sil');
    }

    public function viewTrashed(User $user): bool
    {
        return $user->hasPermissionTo('urun.silinenler');
    }

    public function restore(User $user): bool
    {
        return $user->hasPermissionTo('urun.geriyukle');
    }
}
