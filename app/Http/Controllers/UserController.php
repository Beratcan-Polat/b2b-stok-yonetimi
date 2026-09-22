<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', User::class);

        $kullanicilar = User::with('roles')->orderBy('id')->get();

        return view('kullanicilar.index', compact('kullanicilar'));
    }

    public function create()
    {
        Gate::authorize('create', User::class);

        $roller = Role::orderBy('name')->get();

        return view('kullanicilar.create', compact('roller'));
    }

    public function store(UserRequest $request)
    {
        $veriler = $request->validated();

        $kullanici = User::create([
            'name' => $veriler['name'],
            'email' => $veriler['email'],
            'password' => Hash::make($veriler['password']),
        ]);

        $kullanici->roles()->sync([$veriler['role_id']]);

        return redirect()->route('kullanicilar.index')->with('success', 'Kullanıcı başarıyla eklendi.');
    }

    public function edit(User $kullanici)
    {
        Gate::authorize('update', $kullanici);

        $roller = Role::orderBy('name')->get();

        return view('kullanicilar.edit', compact('kullanici', 'roller'));
    }

    public function update(UserRequest $request, User $kullanici)
    {
        $veriler = $request->validated();

        $kullanici->update([
            'name' => $veriler['name'],
            'email' => $veriler['email'],
        ]);

        if (! empty($veriler['password'])) {
            $kullanici->update([
                'password' => Hash::make($veriler['password']),
            ]);
        }

        if ($kullanici->id !== auth()->id()) {
            $kullanici->roles()->sync([$veriler['role_id']]);
        }

        return redirect()->route('kullanicilar.index')->with('success', 'Kullanıcı başarıyla güncellendi.');
    }

    public function destroy(User $kullanici)
    {
        Gate::authorize('delete', $kullanici);

        if ($kullanici->id === auth()->id()) {
            return redirect()->route('kullanicilar.index')->with('error', 'Kendi hesabınızı silemezsiniz.');
        }

        $kullanici->delete();

        return redirect()->route('kullanicilar.index')->with('success', 'Kullanıcı başarıyla silindi.');
    }
}
