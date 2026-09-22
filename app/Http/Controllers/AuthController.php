<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function girisFormu()
    {
        return view('auth.giris');
    }

    public function giris(LoginRequest $request)
    {
        $request->kimlikDogrula();

        return redirect()->intended(route('anasayfa'));
    }

    public function cikis(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('giris')->with('success', 'Çıkış yapıldı.');
    }
}
