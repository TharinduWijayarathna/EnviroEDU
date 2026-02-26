<?php

use App\Http\Controllers\Admin\ApprovalController as AdminApprovalController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EduBuddyController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Parent\ChildController as ParentChildController;
use App\Http\Controllers\PlayController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\Teacher\BadgeController;
use App\Http\Controllers\Teacher\ClassRoomController;
use App\Http\Controllers\Teacher\MiniGameController;
use App\Http\Controllers\Teacher\ProgressController as TeacherProgressController;
use App\Http\Controllers\Teacher\QuizController;
use App\Http\Controllers\Teacher\TopicController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'home'])->name('home');
Route::get('/join', [LandingController::class, 'join'])->name('landing.join');
Route::get('/platform', [LandingController::class, 'platform'])->name('landing.platform');
Route::get('/how-it-works', [LandingController::class, 'howItWorks'])->name('landing.how-it-works');

Route::middleware('guest')->group(function (): void {
    Route::get('/login/{role}', [AuthController::class, 'showLogin'])->name('login')->where('role', 'admin|teacher|student|parent');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register/{role}', [AuthController::class, 'showRegister'])->name('register')->where('role', 'admin|teacher|student|parent');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/play/quiz/{quiz}', [PlayController::class, 'quiz'])->name('play.quiz');
Route::get('/play/game/{miniGame}', [PlayController::class, 'miniGame'])->name('play.mini-game');

Route::get('/approval/pending', fn () => view('auth.approval-pending'))->name('approval.pending')->middleware('auth');

Route::middleware(['auth', 'approved'])->group(function (): void {
    Route::post('/progress/quiz', [ProgressController::class, 'recordQuiz'])->name('progress.quiz');
    Route::post('/progress/game', [ProgressController::class, 'recordMiniGame'])->name('progress.game');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/admin', [DashboardController::class, 'admin'])->name('dashboard.admin')->middleware('role:admin');
    Route::get('/dashboard/student', [DashboardController::class, 'student'])->name('dashboard.student')->middleware('role:student');
    Route::get('/dashboard/student/badges', [DashboardController::class, 'studentBadges'])->name('dashboard.student.badges')->middleware('role:student');
    Route::get('/dashboard/student/topics', [DashboardController::class, 'studentTopics'])->name('dashboard.student.topics')->middleware('role:student');
    Route::get('/dashboard/student/games', [DashboardController::class, 'studentGames'])->name('dashboard.student.games')->middleware('role:student');
    Route::get('/dashboard/student/quizzes', [DashboardController::class, 'studentQuizzes'])->name('dashboard.student.quizzes')->middleware('role:student');
    Route::get('/dashboard/student/topic/{topic}', [DashboardController::class, 'studentTopic'])->name('dashboard.student.topic')->middleware('role:student');
    Route::post('/edubuddy/chat', [EduBuddyController::class, 'chat'])->name('edubuddy.chat')->middleware('role:student');
    Route::get('/dashboard/teacher', [DashboardController::class, 'teacher'])->name('dashboard.teacher')->middleware('role:teacher');
    Route::get('/dashboard/parent', [DashboardController::class, 'parent'])->name('dashboard.parent')->middleware('role:parent');

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function (): void {
        Route::get('approvals', [AdminApprovalController::class, 'index'])->name('approvals.index');
        Route::post('approvals/{user}/approve', [AdminApprovalController::class, 'approve'])->name('approvals.approve');
    });

    Route::middleware('role:parent')->prefix('parent')->name('parent.')->group(function (): void {
        Route::get('children', [ParentChildController::class, 'index'])->name('children.index');
        Route::post('children', [ParentChildController::class, 'store'])->name('children.store');
        Route::get('children/{child}', [ParentChildController::class, 'show'])->name('children.show');
    });

    Route::middleware('role:teacher')->prefix('teacher')->name('teacher.')->group(function (): void {
        Route::resource('class-rooms', ClassRoomController::class)->parameters(['class-rooms' => 'classRoom']);
        Route::post('class-rooms/{classRoom}/students', [ClassRoomController::class, 'addStudent'])->name('class-rooms.students.store');
        Route::delete('class-rooms/{classRoom}/students/{student}', [ClassRoomController::class, 'removeStudent'])->name('class-rooms.students.destroy');
        Route::resource('topics', TopicController::class)->parameters(['topics' => 'topic']);
        Route::resource('quizzes', QuizController::class);
        Route::post('mini-games/generate', [MiniGameController::class, 'generate'])->name('mini-games.generate');
        Route::resource('mini-games', MiniGameController::class)->parameters(['mini-games' => 'miniGame']);
        Route::post('badges/generate-image', [BadgeController::class, 'generateImage'])->name('badges.generate-image');
        Route::resource('badges', BadgeController::class);
        Route::get('progress', [TeacherProgressController::class, 'index'])->name('progress.index');
        Route::get('progress/students/{student}', [TeacherProgressController::class, 'show'])->name('progress.show');
    });
});
