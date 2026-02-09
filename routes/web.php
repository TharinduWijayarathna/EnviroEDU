<?php

use App\Http\Controllers\EnviroEduController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route(match (Auth::user()->role) {
            'student' => 'student.dashboard',
            'teacher' => 'teacher.dashboard',
            'parent' => 'parent.dashboard',
            default => 'login',
        });
    }

    return redirect()->route('login');
});

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [EnviroEduController::class, 'login'])->name('login');
    Route::post('/login', [EnviroEduController::class, 'loginPost'])->name('login.post');
});

Route::post('/logout', [EnviroEduController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function (): void {
    Route::get('/student/dashboard', [EnviroEduController::class, 'studentDashboard'])->name('student.dashboard');
    Route::get('/teacher/dashboard', [EnviroEduController::class, 'teacherDashboard'])->name('teacher.dashboard');
    Route::get('/parent/dashboard', [EnviroEduController::class, 'parentDashboard'])->name('parent.dashboard');
    Route::get('/parent/dashboard-v2', [EnviroEduController::class, 'parentDashboardV2'])->name('parent.dashboard.v2');

    Route::get('/video-lesson', [EnviroEduController::class, 'videoLesson'])->name('video.lesson');
    Route::get('/video-lesson/{videoLesson}', [EnviroEduController::class, 'videoLessonShow'])->name('video.lesson.show');
    Route::post('/lesson/complete', [EnviroEduController::class, 'completeLesson'])->name('lesson.complete');
    Route::post('/game/complete', [EnviroEduController::class, 'completeGame'])->name('game.complete');

    Route::get('/games', [EnviroEduController::class, 'gamesHub'])->name('games.hub');
    Route::get('/games/quiz', [EnviroEduController::class, 'gamesQuiz'])->name('games.quiz');
    Route::get('/games/living-non-living', [EnviroEduController::class, 'gamesLivingNonLiving'])->name('games.living_non_living');
    Route::get('/games/who-am-i', [EnviroEduController::class, 'gamesWhoAmI'])->name('games.who_am_i');
    Route::get('/games/plant-builder', [EnviroEduController::class, 'gamesPlantBuilder'])->name('games.plant_builder');
    Route::get('/games/plant-matching', [EnviroEduController::class, 'gamesPlantMatching'])->name('games.plant_matching');
    Route::get('/games/habitats-match', [EnviroEduController::class, 'gamesHabitatsMatch'])->name('games.habitats_match');
    Route::get('/games/mini-safari', [EnviroEduController::class, 'gamesMiniSafari'])->name('games.mini_safari');
    Route::get('/games/clean-city', [EnviroEduController::class, 'gamesCleanCity'])->name('games.clean_city');
    Route::get('/games/water-saver', [EnviroEduController::class, 'gamesWaterSaver'])->name('games.water_saver');

    Route::get('/leaderboard/achievements', [EnviroEduController::class, 'leaderboardAchievements'])->name('leaderboard.achievements');
    Route::get('/leaderboard/progress', [EnviroEduController::class, 'leaderboardProgress'])->name('leaderboard.progress');
    Route::get('/student/progress-report/{student?}', [EnviroEduController::class, 'studentProgressReport'])->name('student.progress_report');
    Route::get('/teacher/upload-video', [EnviroEduController::class, 'teacherUploadVideo'])->name('teacher.upload_video');
    Route::post('/teacher/upload-video', [EnviroEduController::class, 'teacherUploadVideoPost'])->name('teacher.upload_video.post');
    Route::get('/logout-confirmation', [EnviroEduController::class, 'logoutConfirmation'])->name('logout.confirmation');
});
