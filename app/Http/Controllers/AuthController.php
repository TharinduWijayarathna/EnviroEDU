<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\ClassRoom;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLoginRoleChoice(): View
    {
        return view('auth.login-choose');
    }

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

                return back()->withErrors(['email' => 'This account is not registered as a '.$role.'.']);
            }
            $request->session()->regenerate();

            if (! $user->isApproved()) {
                return redirect()->route('approval.pending');
            }

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

    public function registerClasses(Request $request): JsonResponse
    {
        $request->validate([
            'school_code' => ['required', 'string', 'exists:schools,slug'],
            'grade' => ['required', 'integer', 'in:4,5'],
        ]);

        $school = School::query()->where('slug', $request->input('school_code'))->first();
        $classes = ClassRoom::query()
            ->where('school_id', $school->id)
            ->where('grade_level', (int) $request->input('grade'))
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($c) => ['id' => $c->id, 'name' => $c->name]);

        return response()->json(['classes' => $classes]);
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $role = $request->input('role', 'student');
        $roleEnum = Role::from($role);

        if ($role === 'admin') {
            $user = DB::transaction(function () use ($request, $roleEnum) {
                $user = User::query()->create([
                    'name' => $request->input('name'),
                    'email' => $request->input('email'),
                    'password' => $request->input('password'),
                    'role' => $roleEnum,
                ]);
                $school = School::query()->create([
                    'name' => $request->input('school_name'),
                    'slug' => $request->input('school_code'),
                    'admin_id' => $user->id,
                ]);
                $user->update(['school_id' => $school->id]);

                return $user->fresh();
            });
        } else {
            $schoolId = null;
            $isApproved = true;
            if (in_array($role, ['teacher', 'student'], true)) {
                $school = School::query()->where('slug', $request->input('school_code'))->firstOrFail();
                $schoolId = $school->id;
                $isApproved = false;
            }
            $userData = [
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => $request->input('password'),
                'role' => $roleEnum,
                'school_id' => $schoolId,
                'is_approved' => $isApproved,
            ];
            if ($role === 'student' && $request->has('grade_level')) {
                $userData['grade_level'] = (int) $request->input('grade_level');
            }
            $user = User::query()->create($userData);

            if ($role === 'student' && $request->filled('class_id')) {
                $classRoom = ClassRoom::query()
                    ->where('id', $request->input('class_id'))
                    ->where('school_id', $schoolId)
                    ->where('grade_level', (int) $request->input('grade_level'))
                    ->first();
                if ($classRoom) {
                    $classRoom->students()->attach($user->id);
                }
            }

            if ($role === 'parent' && $request->filled('child_email')) {
                $student = User::query()
                    ->where('email', $request->input('child_email'))
                    ->where('role', Role::Student)
                    ->first();
                if ($student) {
                    $user->children()->attach($student->id);
                }
            }
        }

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
