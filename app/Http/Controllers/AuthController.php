<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Throwable;

class AuthController extends Controller
{
    public function girisFormu()
    {
        return view('auth.giris');
    }

    public function giris(LoginRequest $request, CartService $cartService): RedirectResponse {
    $request->kimlikDogrula();

    try {
        $needsReview = $cartService->mergeGuestCart();
    } catch (Throwable $exception) {
        report($exception);

        return to_route('sepet.index')
            ->with('error', __('sepet.merge-failed'));
    }

    if ($needsReview) {
        return to_route('sepet.index')
            ->with('error', __('sepet.review'));
    }

    return redirect()->intended(route('anasayfa'));
}

    public function cikis(Request $request)
    {
        $locale = app()->getLocale();

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->put('locale', $locale);

        return redirect()->route('giris')->with('success', __('panel.logged-out'));
    }
}
