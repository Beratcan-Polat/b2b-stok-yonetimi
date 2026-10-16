<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Role::class);

        return view('roller.index');
    }

    public function veri(): JsonResponse
    {
        Gate::authorize('viewAny', Role::class);

        $roller = Role::withCount(['users', 'permissions'])
            ->orderByDesc('is_super_admin')
            ->orderBy('label')
            ->get();

        $veriler = $roller->map(function (Role $rol): array {
            return [
                'label' => $rol->label,
                'name' => $rol->name,
                'users_count' => $rol->users_count,
                'permissions_count' => $rol->permissions_count,
                'is_super_admin' => $rol->isSuperAdmin(),
                'actions' => view('roller._actions', ['rol' => $rol])->render(),
            ];
        });

        return response()->json(['data' => $veriler]);
    }

    public function create()
    {
        Gate::authorize('create', Role::class);

        $izinGruplari = $this->izinGruplari();

        return view('roller.create', compact('izinGruplari'));
    }

    public function store(RoleRequest $request)
    {
        $veriler = $request->validated();

        DB::transaction(function () use ($veriler): void {
            $rol = Role::create([
                'name' => $veriler['name'],
                'label' => $veriler['label'],
            ]);

            $rol->permissions()->sync($veriler['permission_ids'] ?? []);
        });

        return redirect()
            ->route('roller.index')
            ->with('success', 'Rol başarıyla oluşturuldu.');
    }

    public function edit(Role $rol)
    {
        Gate::authorize('update', $rol);

        $rol->load('permissions');

        $izinGruplari = $this->izinGruplari();

        return view('roller.edit', compact('rol', 'izinGruplari'));
    }

    public function update(RoleRequest $request, Role $rol)
    {
        $veriler = $request->validated();

        DB::transaction(function () use ($veriler, $rol): void {
            $hedefRol = Role::whereKey($rol->id)
                ->lockForUpdate()
                ->firstOrFail();

            Gate::authorize('update', $hedefRol);

            $hedefRol->update([
                'name' => $veriler['name'],
                'label' => $veriler['label'],
            ]);

            $hedefRol->permissions()->sync($veriler['permission_ids'] ?? []);
        });

        return redirect()
            ->route('roller.index')
            ->with('success', 'Rol ve yetkileri başarıyla güncellendi.');
    }

    public function destroy(Role $rol)
    {
        Gate::authorize('delete', $rol);

        DB::transaction(function () use ($rol): void {
            $hedefRol = Role::whereKey($rol->id)
                ->lockForUpdate()
                ->firstOrFail();

            Gate::authorize('delete', $hedefRol);

            if ($hedefRol->users()->exists()) {
                throw ValidationException::withMessages([
                    'rol' => 'Bu role bağlı kullanıcılar var. Önce kullanıcıları başka bir role taşıyınız.',
                ]);
            }

            $hedefRol->permissions()->detach();

            $hedefRol->delete();
        });

        return redirect()
            ->route('roller.index')
            ->with('success', 'Rol başarıyla silindi.');
    }

    private function izinGruplari()
    {
        return Permission::orderBy('name')
            ->get()
            ->groupBy(function (Permission $izin): string {
                return explode('.', $izin->name, 2)[0];
            });
    }
}
