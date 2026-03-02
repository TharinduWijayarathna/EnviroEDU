<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name')) - {{ __('messages.common.app_name') }}</title>
    @vite(['resources/css/app.css'])
    @stack('styles')
    <style>
        .eco-preloader { position: fixed; inset: 0; z-index: 9999; background: linear-gradient(135deg, #e8f7f5 0%, #f7fff7 50%, #fff8e1 100%); display: flex; flex-direction: column; align-items: center; justify-content: center; transition: opacity 0.4s ease, visibility 0.4s ease; }
        .eco-preloader.hidden { opacity: 0; visibility: hidden; pointer-events: none; }
        .eco-preloader-logo { width: 120px; height: auto; object-fit: contain; animation: eco-preloader-pulse 1.2s ease-in-out infinite; }
        .eco-preloader-spinner { width: 40px; height: 40px; margin-top: 1.5rem; border: 4px solid rgba(78, 205, 196, 0.3); border-top-color: #4ECDC4; border-radius: 50%; animation: eco-preloader-spin 0.8s linear infinite; }
        @keyframes eco-preloader-pulse { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.85; transform: scale(1.05); } }
        @keyframes eco-preloader-spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body class="eco-body">
    <div class="eco-preloader" id="ecoPreloader" aria-hidden="false">
        <img src="{{ asset('images/logo.png') }}" alt="{{ __('messages.common.app_name') }}" class="eco-preloader-logo">
        <div class="eco-preloader-spinner"></div>
    </div>
    @yield('canvas')
    <div class="eco-ui-overlay">
        @yield('content')
    </div>
    @vite(['resources/js/app.js'])
    @stack('scripts')
    <script>
        (function() {
            function hidePreloader() {
                var el = document.getElementById('ecoPreloader');
                if (el) { el.classList.add('hidden'); el.setAttribute('aria-hidden', 'true'); }
            }
            if (document.readyState === 'complete') { hidePreloader(); }
            else { window.addEventListener('load', hidePreloader); }
        })();
    </script>
</body>
</html>
