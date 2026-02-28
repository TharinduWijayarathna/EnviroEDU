<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
    /**
     * Switch the application locale and redirect back.
     */
    public function switch(Request $request): RedirectResponse
    {
        $locale = $request->input('locale', $request->route('locale'));

        if (in_array($locale, config('app.available_locales', ['en', 'si']), true)) {
            $request->session()->put('locale', $locale);
            App::setLocale($locale);
        }

        return redirect()->back();
    }
}
