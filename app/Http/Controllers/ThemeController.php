<?php

namespace App\Http\Controllers;

use App\Http\Requests\ThemeRequest;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ThemeController extends Controller
{

    public function edit(): View
    {
        Gate::authorize('tema.yonet');
        $temalar = config('themes.options', []);
        $aktifTema = SiteSetting::catalogTheme();
        return view('tema.edit', compact('temalar', 'aktifTema'));
    }

    public function update(ThemeRequest $request): RedirectResponse
    {
        $veriler = $request->validated();

        SiteSetting::updateOrCreate(
            ['key' => 'catalog_theme'],
            ['value' => $veriler['theme']],
        );

        return redirect()->route('tema.edit')->with('success', __('tema.saved'));
    }
}
