<?php

namespace App\Providers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::before(function (
            User $user,
            string $ability,
            array $arguments,
        ) {
            $hedef = $arguments[0] ?? null;

            if ($hedef instanceof Role || $hedef === Role::class) {
                return null;
            }

            if ($user->isSuperAdmin()) {
                return true;
            }

            return null;
        });

        Gate::define('admin', fn (User $user): bool => $user->isSuperAdmin());
    }
}
