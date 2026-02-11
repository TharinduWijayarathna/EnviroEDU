<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PlayController;
use App\Http\Controllers\Teacher\MiniGameController;
use App\Http\Controllers\Teacher\QuizController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('home'))->name('home');

Route::middleware('guest')->group(function (): void {
    Route::get('/login/{role}', [AuthController::class, 'showLogin'])->name('login')->where('role', 'student|teacher|parent');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register/{role}', [AuthController::class, 'showRegister'])->name('register')->where('role', 'student|teacher|parent');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/play/quiz/{quiz}', [PlayController::class, 'quiz'])->name('play.quiz');
Route::get('/play/game/{miniGame}', [PlayController::class, 'miniGame'])->name('play.mini-game');

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/student', [DashboardController::class, 'student'])->name('dashboard.student')->middleware('role:student');
    Route::get('/dashboard/teacher', [DashboardController::class, 'teacher'])->name('dashboard.teacher')->middleware('role:teacher');
    Route::get('/dashboard/parent', [DashboardController::class, 'parent'])->name('dashboard.parent')->middleware('role:parent');

    Route::middleware('role:teacher')->prefix('teacher')->name('teacher.')->group(function (): void {
        Route::resource('quizzes', QuizController::class);
        Route::resource('mini-games', MiniGameController::class)->parameters(['mini-games' => 'miniGame']);
    });
});
