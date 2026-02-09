@extends('layouts.enviroedu')

@section('title', 'Logout - EnviroEdu')

@section('content')
    <div class="text-center max-w-md mx-auto">
        <span class="text-6xl block mb-4">👋</span>
        <h1 class="text-2xl font-bold text-slate-800 mb-2">Log out</h1>
        <p class="text-slate-500 mb-8">Are you sure you want to log out?</p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <button type="button" onclick="window.history.back()" class="px-8 py-3.5 bg-slate-200 hover:bg-slate-300 text-slate-800 font-semibold rounded-xl transition">
                Cancel
            </button>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="w-full sm:w-auto px-8 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl shadow-lg shadow-emerald-600/25 transition">
                    Log out
                </button>
            </form>
        </div>
    </div>
@endsection
