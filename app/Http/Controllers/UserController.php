<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', User::class);

        return view('kullanicilar.index');
    }

    public function veri(): JsonResponse
    {
        Gate::authorize('viewAny', User::class);

        $kullanicilar = User::with('roles')->orderBy('id')->get();

        $veriler = $kullanicilar->map(function (User $kullanici): array {
            return [
                'id' => $kullanici->id,
                'name' => $kullanici->name,
                'email' => $kullanici->email,
                'roles' => $kullanici->roles->pluck('label')->implode(', ') ?: 'Rol atanmamış',
                'is_current' => $kullanici->id === Auth::id(),
                'actions' => view('kullanicilar._actions', [
                    'kullanici' => $kullanici,
                ])->render(),
            ];
        });

        return response()->json(['data' => $veriler]);
    }

    public function create()
    {
        Gate::authorize('create', User::class);

        $rolSorgusu = Role::query();

        /** @var User $oturumKullanicisi */
        $oturumKullanicisi = Auth::user();

        if (! $oturumKullanicisi->isSuperAdmin()) {
            $rolSorgusu->where('is_super_admin', false);
        }

        $roller = $rolSorgusu->with('permissions')->orderBy('label')->get();
        $izinGruplari = $oturumKullanicisi->isSuperAdmin()
            ? Permission::orderBy('name')->get()->groupBy(fn ($izin) => explode('.', $izin->name, 2)[0])
            : collect();

        return view('kullanicilar.create', compact('roller', 'izinGruplari'));
    }

    public function store(UserRequest $request)
    {
        $veriler = $request->validated();

        DB::transaction(function () use ($veriler): void {
            if ($veriler['role_mode'] === 'new') {
                Gate::authorize('create', Role::class);

                $rol = Role::create([
                    'name' => $veriler['new_role_slug'],
                    'label' => $veriler['new_role_name'],
                ]);

                $rol->permissions()->sync($veriler['new_role_permission_ids'] ?? []);
            } else {
                $rol = Role::whereKey($veriler['role_id'])->lockForUpdate()->firstOrFail();

                abort_if($rol->isSuperAdmin() && ! $this->authenticatedUser()->isSuperAdmin(), 403);
            }

            $kullanici = User::create([
                'name' => $veriler['name'],
                'email' => $veriler['email'],
                'password' => Hash::make($veriler['password']),
            ]);

            $kullanici->roles()->sync([
                $rol->id,
            ]);

            if (array_key_exists('user_permissions_present', $veriler)) {
                $this->syncSelectedPermissions($kullanici, $veriler['user_permission_ids'] ?? []);
            } elseif (array_key_exists('permission_overrides', $veriler)) {
                $this->syncPermissionOverrides($kullanici, $veriler['permission_overrides']);
            }
        });

        $donus = Gate::allows('viewAny', User::class) ? 'kullanicilar.index' : 'anasayfa';

        return redirect()
            ->route($donus)
            ->with('success', 'Kullanıcı başarıyla eklendi.');
    }

    public function edit(User $kullanici)
    {
        Gate::authorize('update', $kullanici);

        $rolSorgusu = Role::query();

        /** @var User $oturumKullanicisi */
        $oturumKullanicisi = Auth::user();

        if (! $oturumKullanicisi->isSuperAdmin()) {
            $rolSorgusu->where('is_super_admin', false);
        }

        $roller = $rolSorgusu->with('permissions')->orderBy('label')->get();
        $kullanici->load('permissions');
        $izinGruplari = $oturumKullanicisi->isSuperAdmin()
            ? Permission::orderBy('name')->get()->groupBy(fn ($izin) => explode('.', $izin->name, 2)[0])
            : collect();

        return view('kullanicilar.edit', compact('kullanici', 'roller', 'izinGruplari'));
    }

    public function update(UserRequest $request, User $kullanici)
    {
        $veriler = $request->validated();

        DB::transaction(function () use ($veriler, $kullanici): void {
            Role::where('is_super_admin', true)
                ->lockForUpdate()
                ->firstOrFail();

            $hedefKullanici = User::whereKey($kullanici->id)
                ->lockForUpdate()
                ->firstOrFail();

            Gate::authorize('update', $hedefKullanici);

            $rolDegistirilebilir = $hedefKullanici->id !== Auth::id();

            if ($rolDegistirilebilir) {
                $yeniRol = Role::whereKey($veriler['role_id'])->lockForUpdate()->firstOrFail();

                abort_if($yeniRol->isSuperAdmin() && ! $this->authenticatedUser()->isSuperAdmin(), 403);

                if (
                    $hedefKullanici->isSuperAdmin()
                    && ! $yeniRol->isSuperAdmin()
                ) {
                    $superAdminSayisi = User::whereHas(
                        'roles',
                        function ($query): void {
                            $query->where('is_super_admin', true);
                        },
                    )->count();

                    if ($superAdminSayisi <= 1) {
                        throw ValidationException::withMessages([
                            'role_id' => 'Son Süper Admin normal bir role geçirilemez.',
                        ]);
                    }
                }
            }

            $guncellenecekAlanlar = [
                'name' => $veriler['name'],
                'email' => $veriler['email'],
            ];

            if ($this->authenticatedUser()->isSuperAdmin() && ! empty($veriler['password'])) {
                $guncellenecekAlanlar['password'] = Hash::make(
                    $veriler['password'],
                );
            }

            $hedefKullanici->update($guncellenecekAlanlar);

            if ($rolDegistirilebilir) {
                $hedefKullanici->roles()->sync([
                    $yeniRol->id,
                ]);
            }

            if ($hedefKullanici->isSuperAdmin()) {
                $hedefKullanici->permissions()->detach();
            } elseif (array_key_exists('user_permissions_present', $veriler)) {
                $this->syncSelectedPermissions($hedefKullanici, $veriler['user_permission_ids'] ?? []);
            } elseif (array_key_exists('permission_overrides', $veriler)) {
                $this->syncPermissionOverrides($hedefKullanici, $veriler['permission_overrides']);
            }
        });

        return redirect()
            ->route('kullanicilar.index')
            ->with('success', 'Kullanıcı başarıyla güncellendi.');
    }

    public function destroy(User $kullanici)
    {
        Gate::authorize('delete', $kullanici);

        if ($kullanici->id === Auth::id()) {
            return redirect()
                ->route('kullanicilar.index')
                ->with('error', 'Kendi hesabınızı silemezsiniz.');
        }

        DB::transaction(function () use ($kullanici): void {
            Role::where('is_super_admin', true)
                ->lockForUpdate()
                ->firstOrFail();

            $hedefKullanici = User::whereKey($kullanici->id)
                ->lockForUpdate()
                ->firstOrFail();

            Gate::authorize('delete', $hedefKullanici);

            if ($hedefKullanici->isSuperAdmin()) {
                $superAdminSayisi = User::whereHas(
                    'roles',
                    function ($query): void {
                        $query->where('is_super_admin', true);
                    },
                )->count();

                if ($superAdminSayisi <= 1) {
                    throw ValidationException::withMessages([
                        'kullanici' => 'Son Süper Admin hesabı silinemez.',
                    ]);
                }
            }

            DB::table('sessions')
                ->where('user_id', $hedefKullanici->id)
                ->delete();

            DB::table('password_reset_tokens')
                ->where('email', $hedefKullanici->email)
                ->delete();

            $hedefKullanici->roles()->detach();

            $hedefKullanici->delete();
        });

        return redirect()
            ->route('kullanicilar.index')
            ->with('success', 'Kullanıcı başarıyla silindi.');

    }

    private function syncSelectedPermissions(User $user, array $ids): void
    {
        abort_unless($this->authenticatedUser()->isSuperAdmin(), 403);

        if ($user->isSuperAdmin()) {
            $user->permissions()->detach();

            return;
        }

        $permissions = Permission::all();
        $selected = $permissions->whereIn('id', $ids)->pluck('name')->all();
        $inherited = Permission::whereHas('roles.users', fn ($query) => $query->where('users.id', $user->id))->pluck('name')->all();
        $sync = [];

        foreach ($permissions as $permission) {
            $allowed = in_array($permission->name, $selected, true);

            if ($allowed !== in_array($permission->name, $inherited, true)) {
                $sync[$permission->id] = ['allowed' => $allowed];
            }
        }

        $user->permissions()->sync($sync);
    }

    private function syncPermissionOverrides(User $user, array $overrides): void
    {
        abort_unless($this->authenticatedUser()->isSuperAdmin(), 403);

        if ($user->isSuperAdmin()) {
            $user->permissions()->detach();

            return;
        }

        $permissions = Permission::all()->keyBy('id');
        $states = [];

        foreach ($overrides as $override) {
            $permission = $permissions->get($override['permission_id']);

            if (! $permission) {
                throw ValidationException::withMessages([
                    'permission_overrides' => 'Seçilen izin bulunamadı.',
                ]);
            }

            $states[$permission->name] = $override['state'];
        }

        $sync = [];

        foreach ($permissions as $permission) {
            $state = $states[$permission->name] ?? 'inherit';

            if ($state !== 'inherit') {
                $sync[$permission->id] = ['allowed' => $state === 'allow'];
            }
        }

        $user->permissions()->sync($sync);
    }

    private function authenticatedUser(): User
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            throw new AuthenticationException;
        }

        return $user;
    }
}
