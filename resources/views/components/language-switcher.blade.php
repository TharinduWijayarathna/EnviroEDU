@php
    $currentLocale = app()->getLocale();
    $availableLocales = config('app.available_locales', ['en', 'si']);
@endphp
<div class="eco-language-switcher" role="group" aria-label="{{ __('messages.nav.language') }}">
    @foreach ($availableLocales as $locale)
        <a href="{{ route('locale.switch', ['locale' => $locale]) }}"
           class="eco-language-link {{ $currentLocale === $locale ? 'eco-language-link-active' : '' }}"
           hreflang="{{ $locale }}"
           @if ($currentLocale === $locale) aria-current="true" @endif>
            {{ __('messages.locale.' . $locale) }}
        </a>
    @endforeach
</div>
