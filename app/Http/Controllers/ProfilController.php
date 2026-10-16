<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfilRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

class ProfilController extends Controller
{
    public function goster()
    {
        return view('profil.goster');

    }

    public function sifreGuncelle(ProfilRequest $request): RedirectResponse
    {
        $veriler = $request->validated();

        $request->user()->update([
            'password' => Hash::make($veriler['password']),
        ]);

        return redirect()->route('profil.goster')
            ->with('success', 'Şifre başarıyla güncellendi.');
    }
}
