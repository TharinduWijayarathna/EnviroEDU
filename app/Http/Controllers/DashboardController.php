<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\ClassRoom;
use App\Models\MiniGame;
use App\Models\Quiz;
use App\Models\Topic;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): RedirectResponse
    {
        $user = auth()->user();
        $role = $user->role?->value ?? 'student';

        return redirect()->route("dashboard.{$role}");
    }

    public function admin(): View
    {
        $school = auth()->user()->school;
        $pendingTeachersCount = $school?->pendingTeachers()->count() ?? 0;
        $pendingStudentsCount = $school?->pendingStudents()->count() ?? 0;

        return view('dashboard.admin', [
            'school' => $school,
            'pendingTeachersCount' => $pendingTeachersCount,
            'pendingStudentsCount' => $pendingStudentsCount,
        ]);
    }

    public function student(): View
    {
        return view('dashboard.student', [
            'studentPage' => 'dashboard',
            'studentLayoutTitle' => 'My Learning',
            'studentFullWidth' => true,
        ]);
    }

    public function studentBadges(): View
    {
        $earnedBadges = auth()->user()->badges()->orderByPivot('earned_at', 'desc')->get();

        return view('dashboard.student-badges', [
            'studentPage' => 'dashboard',
            'studentLayoutTitle' => 'My badges',
            'studentFullWidth' => true,
            'earnedBadges' => $earnedBadges,
        ]);
    }

    public function studentTopics(): View
    {
        return view('dashboard.student-topics', [
            'studentPage' => 'dashboard',
            'studentLayoutTitle' => 'Topics',
            'studentFullWidth' => true,
        ]);
    }

    public function studentGames(): View
    {
        return view('dashboard.student-games', [
            'studentPage' => 'dashboard',
            'studentLayoutTitle' => 'Games',
            'studentFullWidth' => true,
        ]);
    }

    public function studentQuizzes(): View
    {
        return view('dashboard.student-quizzes', [
            'studentPage' => 'dashboard',
            'studentLayoutTitle' => 'Quizzes',
            'studentFullWidth' => true,
        ]);
    }

    public function studentTopic(Topic $topic): View
    {
        if (! $topic->is_published) {
            abort(404);
        }
        $topic->load([
            'quizzes' => fn ($q) => $q->where('is_published', true),
            'miniGames' => fn ($q) => $q->where('is_published', true)->with('gameTemplate'),
        ]);

        return view('dashboard.student-topic', [
            'studentPage' => 'dashboard',
            'studentLayoutTitle' => $topic->title,
            'studentFullWidth' => true,
            'topic' => $topic,
        ]);
    }

    public function teacher(): View
    {
        $userId = auth()->id();
        $classCount = ClassRoom::query()->where('user_id', $userId)->count();
        $studentCount = ClassRoom::query()
            ->where('user_id', $userId)
            ->withCount('students')
            ->get()
            ->sum('students_count');
        $topicCount = Topic::query()->where('user_id', $userId)->count();
        $quizCount = Quiz::query()->where('user_id', $userId)->count();
        $miniGameCount = MiniGame::query()->where('user_id', $userId)->count();
        $topicIds = Topic::query()->where('user_id', $userId)->pluck('id');
        $badgeCount = Badge::query()->whereIn('topic_id', $topicIds)->count();

        return view('dashboard.teacher', compact('classCount', 'studentCount', 'topicCount', 'quizCount', 'miniGameCount', 'badgeCount'));
    }

    public function parent(): View
    {
        $children = auth()->user()->children()->orderBy('name')->get();

        return view('dashboard.parent', compact('children'));
    }
}
