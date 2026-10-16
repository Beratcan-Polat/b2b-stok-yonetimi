<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->roles()->where('is_super_admin', true)->exists();
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class)->withPivot('allowed');
    }

    public function hasRole(string $rol): bool
    {
        return $this->roles->contains('name', $rol);
    }

    public function hasPermissionTo(string $izin): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $overrides = $this->permissions()->get()->mapWithKeys(
            fn (Permission $permission) => [$permission->name => (bool) $permission->pivot->allowed],
        )->all();

        $rolePermissions = Permission::whereHas(
            'roles.users', fn ($query) => $query->where('users.id', $this->id),
        )->pluck('name')->all();

        return array_key_exists($izin, $overrides)
            ? $overrides[$izin]
            : in_array($izin, $rolePermissions, true);
    }
}
