<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(string $role): View
    {
        $this->validateRole($role);

        return view('auth.login', [
            'role' => $role,
            'roleLabel' => ucfirst($role),
        ]);
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $role = $request->input('role', 'student');
        $this->validateRole($role);

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $user = Auth::user();
            if (! $user->hasRole($role)) {
                Auth::logout();

                return back()->withErrors(['email' => 'This account is not registered as a ' . $role . '.']);
            }
            $request->session()->regenerate();

            return redirect()->intended(route("dashboard.{$role}"));
        }

        return back()->withErrors(['email' => 'The provided credentials do not match our records.']);
    }

    public function showRegister(string $role): View
    {
        $this->validateRole($role);

        return view('auth.register', [
            'role' => $role,
            'roleLabel' => ucfirst($role),
        ]);
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $role = $request->input('role', 'student');
        $roleEnum = Role::from($role);

        $user = User::query()->create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'role' => $roleEnum,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route("dashboard.{$role}"));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function validateRole(string $role): void
    {
        Role::from($role);
    }
}
