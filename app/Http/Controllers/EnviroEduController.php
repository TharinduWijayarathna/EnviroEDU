<?php

namespace App\Http\Controllers;

use App\Http\Requests\EnviroEdu\LoginRequest;
use App\Http\Requests\EnviroEdu\UploadVideoLessonRequest;
use App\Models\GameScore;
use App\Models\LessonCompletion;
use App\Models\User;
use App\Models\VideoLesson;
use App\Services\EnviroEduProgressService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EnviroEduController extends Controller
{
    public function __construct(
        private EnviroEduProgressService $progressService
    ) {}

    public function login(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route(match (Auth::user()->role) {
                'student' => 'student.dashboard',
                'teacher' => 'teacher.dashboard',
                'parent' => 'parent.dashboard',
                default => 'login',
            });
        }

        return view('enviroedu.login');
    }

    public function loginPost(LoginRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if (! Auth::attempt(
            ['email' => $validated['email'], 'password' => $validated['password']],
            $request->boolean('remember')
        )) {
            return back()->withErrors([
                'email' => __('auth.failed'),
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = Auth::user();
        if ($user->role !== $validated['role']) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'role' => __('The selected role does not match your account.'),
            ])->onlyInput('email');
        }

        return match ($validated['role']) {
            'student' => redirect()->intended(route('student.dashboard')),
            'teacher' => redirect()->intended(route('teacher.dashboard')),
            'parent' => redirect()->intended(route('parent.dashboard')),
            default => redirect()->route('login'),
        };
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function studentDashboard(): View
    {
        $user = Auth::user();
        $progressPercent = $this->progressService->progressPercentForUser($user);
        $achievements = $user->achievements()->get();

        return view('enviroedu.student-dashboard', [
            'user' => $user,
            'progressPercent' => $progressPercent,
            'achievements' => $achievements,
        ]);
    }

    public function teacherDashboard(): View
    {
        $students = User::query()
            ->where('role', 'student')
            ->withCount(['lessonCompletions', 'gameScores'])
            ->get()
            ->map(function (User $student) {
                return [
                    'user' => $student,
                    'progressPercent' => $this->progressService->progressPercentForUser($student),
                ];
            });

        return view('enviroedu.teacher-dashboard', [
            'user' => Auth::user(),
            'students' => $students,
        ]);
    }

    public function parentDashboard(): View
    {
        $user = Auth::user();
        $children = $user->children()->get()->map(function (User $child) {
            return [
                'user' => $child,
                'progressPercent' => $this->progressService->progressPercentForUser($child),
                'totalPoints' => $this->progressService->totalPointsForUser($child),
                'achievements' => $child->achievements()->get(),
            ];
        });

        return view('enviroedu.parent-dashboard', [
            'user' => $user,
            'children' => $children,
        ]);
    }

    public function parentDashboardV2(): View
    {
        return view('enviroedu.parent-dashboard-v2', [
            'user' => Auth::user(),
        ]);
    }

    public function videoLesson(): View
    {
        $videoLessons = VideoLesson::query()->orderBy('created_at')->get();
        $firstLesson = $videoLessons->first();

        return view('enviroedu.video-lesson', [
            'videoLessons' => $videoLessons,
            'currentLesson' => $firstLesson,
        ]);
    }

    public function videoLessonShow(VideoLesson $videoLesson): View
    {
        return view('enviroedu.video-lesson', [
            'videoLessons' => VideoLesson::query()->orderBy('created_at')->get(),
            'currentLesson' => $videoLesson,
        ]);
    }

    public function gamesHub(): View
    {
        return view('enviroedu.games-hub');
    }

    public function gamesQuiz(): View
    {
        return view('enviroedu.quiz-game');
    }

    public function gamesLivingNonLiving(): View
    {
        return view('enviroedu.living-non-living-game');
    }

    public function gamesWhoAmI(): View
    {
        return view('enviroedu.who-am-i-game');
    }

    public function gamesPlantBuilder(): View
    {
        return view('enviroedu.plant-builder-game');
    }

    public function gamesPlantMatching(): View
    {
        return view('enviroedu.plant-matching-quiz');
    }

    public function gamesHabitatsMatch(): View
    {
        return view('enviroedu.habitats-match-game');
    }

    public function gamesMiniSafari(): View
    {
        return view('enviroedu.mini-safari-quiz');
    }

    public function gamesCleanCity(): View
    {
        return view('enviroedu.clean-city-game');
    }

    public function gamesWaterSaver(): View
    {
        return view('enviroedu.water-saver-challenge');
    }

    public function leaderboardAchievements(): View
    {
        $leaderboard = $this->progressService->leaderboardByPoints(10);
        $allAchievements = \App\Models\Achievement::orderBy('name')->get();
        $userAchievements = Auth::user()->achievements()->get();

        return view('enviroedu.achievements-leaderboard', [
            'leaderboard' => $leaderboard,
            'allAchievements' => $allAchievements,
            'userAchievements' => $userAchievements,
        ]);
    }

    public function leaderboardProgress(): View
    {
        $user = Auth::user();
        $progressPercent = $this->progressService->progressPercentForUser($user);
        $totalPoints = $this->progressService->totalPointsForUser($user);
        $leaderboard = $this->progressService->leaderboardByPoints(10);

        return view('enviroedu.progress-leaderboard', [
            'user' => $user,
            'progressPercent' => $progressPercent,
            'totalPoints' => $totalPoints,
            'leaderboard' => $leaderboard,
        ]);
    }

    public function studentProgressReport(?User $student = null): View
    {
        $currentUser = Auth::user();
        if ($currentUser->role === 'teacher' && $student === null) {
            $students = User::query()->where('role', 'student')->orderBy('name')->get();

            return view('enviroedu.student-progress-report', [
                'student' => null,
                'students' => $students,
                'teacherSelecting' => true,
            ]);
        }

        $reportUser = $student ?? $currentUser;

        if ($student !== null && $currentUser->role !== 'teacher') {
            abort(403);
        }

        $progressPercent = $this->progressService->progressPercentForUser($reportUser);
        $totalPoints = $this->progressService->totalPointsForUser($reportUser);
        $averageScore = $this->progressService->averageScoreForUser($reportUser);
        $weeklyRank = $this->progressService->weeklyRankForUser($reportUser);
        $lessonsCompleted = $reportUser->lessonCompletions()->pluck('video_lesson_id')->unique()->count();
        $totalLessons = VideoLesson::count();
        $achievements = $reportUser->achievements()->get();
        $gameScores = $reportUser->gameScores()
            ->orderByDesc('completed_at')
            ->get()
            ->map(fn ($s) => [
                'name' => $this->gameSlugToName($s->game_slug),
                'score' => $s->max_score > 0 ? (int) round(($s->score / $s->max_score) * 100) : $s->score,
            ]);

        return view('enviroedu.student-progress-report', [
            'student' => $reportUser,
            'progressPercent' => $progressPercent,
            'totalPoints' => $totalPoints,
            'averageScore' => $averageScore,
            'weeklyRank' => $weeklyRank,
            'lessonsCompleted' => $lessonsCompleted,
            'totalLessons' => $totalLessons,
            'achievements' => $achievements,
            'gameScores' => $gameScores,
        ]);
    }

    public function teacherUploadVideo(): View
    {
        return view('enviroedu.upload-video-lesson');
    }

    public function teacherUploadVideoPost(UploadVideoLessonRequest $request): RedirectResponse
    {
        $path = null;
        if ($request->hasFile('video')) {
            $path = $request->file('video')->store('video-lessons', 'public');
        }

        VideoLesson::create([
            'title' => $request->validated('title'),
            'grade_level' => $request->validated('grade_level'),
            'video_path' => $path,
            'key_points' => $request->validated('key_points'),
        ]);

        return redirect()->route('teacher.dashboard')->with('status', __('Video lesson uploaded successfully.'));
    }

    public function logoutConfirmation(): View
    {
        return view('enviroedu.logout-confirmation');
    }

    public function completeLesson(Request $request): RedirectResponse
    {
        $request->validate(['video_lesson_id' => ['required', 'exists:video_lessons,id']]);

        $user = Auth::user();
        $exists = LessonCompletion::query()
            ->where('user_id', $user->id)
            ->where('video_lesson_id', $request->video_lesson_id)
            ->exists();

        if (! $exists) {
            LessonCompletion::create([
                'user_id' => $user->id,
                'video_lesson_id' => $request->video_lesson_id,
                'completed_at' => now(),
            ]);
        }

        return back();
    }

    public function completeGame(Request $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'game_slug' => ['required', 'string', 'in:'.implode(',', EnviroEduProgressService::GAME_SLUGS)],
            'score' => ['required', 'integer', 'min:0', 'max:1000'],
            'max_score' => ['nullable', 'integer', 'min:1', 'max:1000'],
        ]);

        $user = Auth::user();
        GameScore::create([
            'user_id' => $user->id,
            'game_slug' => $validated['game_slug'],
            'score' => $validated['score'],
            'max_score' => $validated['max_score'] ?? 100,
            'completed_at' => now(),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back();
    }

    private function gameSlugToName(string $slug): string
    {
        return match ($slug) {
            'quiz' => 'Alive Check Quiz',
            'living_non_living' => 'Living Lab',
            'who_am_i' => 'Who Am I?',
            'plant_builder' => 'Plant Builder',
            'plant_matching' => 'Plant Match',
            'habitats_match' => 'Habitat Hero',
            'mini_safari' => 'Mini Safari Quiz',
            'clean_city' => 'Clean City',
            'water_saver' => 'Water Saver',
            default => str_replace('_', ' ', ucfirst($slug)),
        };
    }
}
