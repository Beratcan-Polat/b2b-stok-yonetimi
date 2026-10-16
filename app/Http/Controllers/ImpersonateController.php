<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonateController extends Controller
{
    public function baslat(Request $request, User $kullanici): RedirectResponse
    {
        $superAdmin = $request->user();

        $request->session()->put('impersonator_id', $superAdmin->id);

        Auth::login($kullanici);

        return redirect()->route('anasayfa')
            ->with('success', $kullanici->name.' hesabına geçildi.');
    }

    public function geriDon(Request $request): RedirectResponse
    {
        $superAdminId = $request->session()->get('impersonator_id');

        $superAdmin = User::find($superAdminId);

        $request->session()->forget('impersonator_id');

        Auth::logoutCurrentDevice();
        Auth::login($superAdmin);

        return redirect()->route('anasayfa')
            ->with('success', 'Süper admin hesabına geri dönüldü.');
    }
}
