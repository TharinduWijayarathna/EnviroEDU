<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuizRequest;
use App\Http\Requests\UpdateQuizRequest;
use App\Models\Quiz;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuizController extends Controller
{
    public function index(): View
    {
        $quizzes = Quiz::query()
            ->where('user_id', auth()->id())
            ->withCount('questions')
            ->latest()
            ->paginate(15);

        return view('teacher.quizzes.index', compact('quizzes'));
    }

    public function create(): View
    {
        return view('teacher.quizzes.create');
    }

    public function store(StoreQuizRequest $request): RedirectResponse
    {
        $quiz = Quiz::query()->create([
            'user_id' => auth()->id(),
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'grade_level' => $request->input('grade_level'),
            'is_published' => $request->boolean('is_published'),
        ]);

        foreach ($request->input('questions', []) as $index => $q) {
            $question = $quiz->questions()->create([
                'question_text' => $q['question_text'],
                'order' => $q['order'] ?? $index,
            ]);
            foreach ($q['options'] ?? [] as $optIndex => $opt) {
                $question->options()->create([
                    'option_text' => $opt['option_text'],
                    'is_correct' => $opt['is_correct'] ?? false,
                    'order' => $opt['order'] ?? $optIndex,
                ]);
            }
        }

        return redirect()->route('teacher.quizzes.index')->with('status', 'Quiz created.');
    }

    public function show(Quiz $quiz): View|RedirectResponse
    {
        if ($quiz->user_id !== auth()->id()) {
            abort(403);
        }
        $quiz->load('questions.options');

        return view('teacher.quizzes.show', compact('quiz'));
    }

    public function edit(Quiz $quiz): View|RedirectResponse
    {
        if ($quiz->user_id !== auth()->id()) {
            abort(403);
        }
        $quiz->load('questions.options');

        return view('teacher.quizzes.edit', compact('quiz'));
    }

    public function update(UpdateQuizRequest $request, Quiz $quiz): RedirectResponse
    {
        if ($quiz->user_id !== auth()->id()) {
            abort(403);
        }

        $quiz->update([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'grade_level' => $request->input('grade_level'),
            'is_published' => $request->boolean('is_published'),
        ]);

        $existingQuestionIds = [];
        foreach ($request->input('questions', []) as $index => $q) {
            $question = isset($q['id'])
                ? $quiz->questions()->find($q['id'])
                : null;
            if ($question) {
                $question->update([
                    'question_text' => $q['question_text'],
                    'order' => $q['order'] ?? $index,
                ]);
                $existingQuestionIds[] = $question->id;
            } else {
                $question = $quiz->questions()->create([
                    'question_text' => $q['question_text'],
                    'order' => $q['order'] ?? $index,
                ]);
                $existingQuestionIds[] = $question->id;
            }

            $existingOptionIds = [];
            foreach ($q['options'] ?? [] as $optIndex => $opt) {
                $option = isset($opt['id'])
                    ? $question->options()->find($opt['id'])
                    : null;
                if ($option) {
                    $option->update([
                        'option_text' => $opt['option_text'],
                        'is_correct' => $opt['is_correct'] ?? false,
                        'order' => $opt['order'] ?? $optIndex,
                    ]);
                    $existingOptionIds[] = $option->id;
                } else {
                    $option = $question->options()->create([
                        'option_text' => $opt['option_text'],
                        'is_correct' => $opt['is_correct'] ?? false,
                        'order' => $opt['order'] ?? $optIndex,
                    ]);
                    $existingOptionIds[] = $option->id;
                }
            }
            $question->options()->whereNotIn('id', $existingOptionIds)->delete();
        }
        $quiz->questions()->whereNotIn('id', $existingQuestionIds)->delete();

        return redirect()->route('teacher.quizzes.index')->with('status', 'Quiz updated.');
    }

    public function destroy(Request $request, Quiz $quiz): RedirectResponse
    {
        if ($quiz->user_id !== auth()->id()) {
            abort(403);
        }
        $quiz->delete();

        return redirect()->route('teacher.quizzes.index')->with('status', 'Quiz deleted.');
    }
}
