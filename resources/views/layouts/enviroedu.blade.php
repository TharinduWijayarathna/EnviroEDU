<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'EnviroEdu')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-emerald-600 via-teal-600 to-cyan-700 text-slate-800 antialiased">
    <div class="w-full flex flex-col items-center min-h-screen p-4 sm:p-6 lg:p-8 py-8">
        <main class="w-full max-w-4xl my-auto bg-white rounded-2xl shadow-2xl overflow-hidden">
            @hasSection('backUrl')
            <div class="px-6 pt-6 pb-0">
                <a href="@yield('backUrl')" class="inline-flex items-center gap-2 text-slate-600 hover:text-emerald-600 font-medium transition-colors">
                    <span aria-hidden="true">←</span> Back
                </a>
            </div>
            @endif
            <div class="p-6 sm:p-8 lg:p-10">
                @yield('content')
            </div>
        </main>
    </div>
    @stack('scripts')
</body>
</html>
