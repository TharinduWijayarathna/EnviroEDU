<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTopicRequest;
use App\Http\Requests\UpdateTopicRequest;
use App\Models\Topic;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TopicController extends Controller
{
    public function index(): View
    {
        $topics = Topic::query()
            ->where('user_id', auth()->id())
            ->withCount(['quizzes', 'miniGames'])
            ->orderBy('order')
            ->orderBy('title')
            ->paginate(15);

        return view('teacher.topics.index', compact('topics'));
    }

    public function create(): View
    {
        return view('teacher.topics.create');
    }

    public function store(StoreTopicRequest $request): RedirectResponse
    {
        Topic::query()->create([
            'user_id' => auth()->id(),
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'grade_level' => $request->input('grade_level') ? (int) $request->input('grade_level') : null,
            'video_url' => $request->input('video_url'),
            'order' => (int) ($request->input('order') ?? 0),
            'is_published' => $request->boolean('is_published'),
        ]);

        return redirect()->route('teacher.topics.index')->with('status', 'Topic created.');
    }

    public function show(Topic $topic): View|RedirectResponse
    {
        if ($topic->user_id !== auth()->id()) {
            abort(403);
        }
        $topic->load(['quizzes', 'miniGames.gameTemplate']);

        return view('teacher.topics.show', compact('topic'));
    }

    public function edit(Topic $topic): View|RedirectResponse
    {
        if ($topic->user_id !== auth()->id()) {
            abort(403);
        }

        return view('teacher.topics.edit', compact('topic'));
    }

    public function update(UpdateTopicRequest $request, Topic $topic): RedirectResponse
    {
        if ($topic->user_id !== auth()->id()) {
            abort(403);
        }

        $topic->update([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'grade_level' => $request->input('grade_level') ? (int) $request->input('grade_level') : null,
            'video_url' => $request->input('video_url'),
            'order' => (int) ($request->input('order') ?? 0),
            'is_published' => $request->boolean('is_published'),
        ]);

        return redirect()->route('teacher.topics.index')->with('status', 'Topic updated.');
    }

    public function destroy(Topic $topic): RedirectResponse
    {
        if ($topic->user_id !== auth()->id()) {
            abort(403);
        }
        $topic->delete();

        return redirect()->route('teacher.topics.index')->with('status', 'Topic deleted.');
    }
}
