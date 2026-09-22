<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('kullanici.listele');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('kullanici.ekle');
    }

    public function update(User $user, User $hedef): bool
    {
        return $user->hasPermissionTo('kullanici.duzenle');
    }

    public function delete(User $user, User $hedef): bool
    {
        return $user->hasPermissionTo('kullanici.sil');
    }
}
