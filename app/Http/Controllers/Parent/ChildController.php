<?php

namespace App\Http\Controllers\Parent;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChildController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->route('dashboard.parent');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $student = User::query()
            ->where('email', $validated['email'])
            ->where('role', Role::Student)
            ->first();

        if (! $student) {
            return back()->withErrors(['email' => 'No student account found with this email.']);
        }

        if (auth()->user()->children()->where('student_id', $student->id)->exists()) {
            return back()->with('status', 'Already linked.');
        }

        auth()->user()->children()->attach($student->id);

        return back()->with('status', 'Child linked successfully.');
    }

    public function show(User $child): View|RedirectResponse
    {
        if (! auth()->user()->children()->where('student_id', $child->id)->exists()) {
            abort(403);
        }
        if ($child->role !== Role::Student) {
            abort(404);
        }

        $child->load([
            'quizAttempts' => fn ($q) => $q->with('quiz.topic')->latest('completed_at')->limit(100),
            'miniGameAttempts' => fn ($q) => $q->with('miniGame')->latest('completed_at')->limit(50),
            'badges',
        ]);

        $weaknesses = $this->computeWeaknesses($child);

        return view('parent.children.show', [
            'child' => $child,
            'weaknesses' => $weaknesses,
        ]);
    }

    /**
     * @return array<int, array{title: string, average: float, attempts: int}>
     */
    private function computeWeaknesses(User $child): array
    {
        $attempts = $child->quizAttempts;
        if ($attempts->isEmpty()) {
            return [];
        }

        $byQuiz = $attempts->groupBy('quiz_id');
        $out = [];
        foreach ($byQuiz as $quizId => $group) {
            $quiz = $group->first()->quiz;
            $totalQuestions = $group->sum('total_questions');
            $totalScore = $group->sum('score');
            $average = $totalQuestions > 0 ? round(100 * $totalScore / $totalQuestions, 1) : 0;
            if ($average < 70 && $group->count() >= 1) {
                $out[] = [
                    'title' => $quiz?->title ?? 'Quiz',
                    'average' => $average,
                    'attempts' => $group->count(),
                ];
            }
        }

        return $out;
    }
}
