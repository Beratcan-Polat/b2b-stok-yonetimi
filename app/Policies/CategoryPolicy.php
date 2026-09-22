<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('kategori.listele');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('kategori.ekle');
    }

    public function update(User $user, Category $kategori): bool
    {
        return $user->hasPermissionTo('kategori.duzenle');
    }

    public function delete(User $user, Category $kategori): bool
    {
        return $user->hasPermissionTo('kategori.sil');
    }
}
