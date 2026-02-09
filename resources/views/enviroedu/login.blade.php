@extends('layouts.enviroedu')

@section('title', 'Login - EnviroEdu')

@section('content')
    <header class="text-center mb-10">
        <h1 class="text-3xl font-bold text-slate-800 tracking-tight">EnviroEdu</h1>
        <p class="text-slate-500 mt-1">Engaging Environmental Learning</p>
    </header>

    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
        @csrf
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Enter your email" required autofocus
                class="w-full px-4 py-3 border border-slate-300 rounded-xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
        </div>
        <div>
            <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required
                class="w-full px-4 py-3 border border-slate-300 rounded-xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
        </div>
        <div>
            <label for="role" class="block text-sm font-medium text-slate-700 mb-1.5">Role</label>
            <select id="role" name="role" required
                class="w-full px-4 py-3 border border-slate-300 rounded-xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                <option value="">Choose your role</option>
                <option value="student" {{ old('role') === 'student' ? 'selected' : '' }}>Student</option>
                <option value="teacher" {{ old('role') === 'teacher' ? 'selected' : '' }}>Teacher</option>
                <option value="parent" {{ old('role') === 'parent' ? 'selected' : '' }}>Parent</option>
            </select>
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" id="remember" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}
                class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
            <label for="remember" class="text-sm text-slate-600">Remember me</label>
        </div>
        <button type="submit" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl shadow-lg shadow-emerald-600/25 hover:shadow-emerald-600/30 transition">
            Sign in
        </button>
    </form>

    <p class="text-center mt-6 text-sm text-slate-500">
        <a href="#" class="text-emerald-600 hover:text-emerald-700 font-medium">Forgot password?</a>
    </p>
@endsection
