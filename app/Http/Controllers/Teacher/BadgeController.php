<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBadgeRequest;
use App\Http\Requests\UpdateBadgeRequest;
use App\Models\Badge;
use App\Models\Topic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BadgeController extends Controller
{
    public function index(): View
    {
        $topicIds = Topic::query()->where('user_id', auth()->id())->pluck('id');
        $badges = Badge::query()
            ->whereIn('topic_id', $topicIds)
            ->with('topic')
            ->orderBy('order')
            ->orderBy('name')
            ->paginate(15);

        return view('teacher.badges.index', compact('badges'));
    }

    public function create(): View|RedirectResponse
    {
        $topics = Topic::query()
            ->where('user_id', auth()->id())
            ->orderBy('order')
            ->orderBy('title')
            ->get();

        if ($topics->isEmpty()) {
            return redirect()->route('teacher.badges.index')
                ->with('status', 'Create a topic first, then you can add badges for it.');
        }

        return view('teacher.badges.create', compact('topics'));
    }

    public function store(StoreBadgeRequest $request): RedirectResponse
    {
        $slug = Str::slug($request->input('name'));
        $baseSlug = $slug;
        $i = 0;
        while (Badge::query()->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.(++$i);
        }

        Badge::query()->create([
            'topic_id' => $request->input('topic_id'),
            'name' => $request->input('name'),
            'slug' => $slug,
            'description' => $request->input('description'),
            'icon' => $request->input('icon') ?: '🏆',
            'award_for' => $request->input('award_for'),
            'order' => 0,
        ]);

        return redirect()->route('teacher.badges.index')->with('status', 'Badge created.');
    }

    public function show(Badge $badge): View|RedirectResponse
    {
        if (! $badge->topic_id || $badge->topic->user_id !== auth()->id()) {
            abort(404);
        }
        $badge->load('topic');

        return view('teacher.badges.show', compact('badge'));
    }

    public function edit(Badge $badge): View|RedirectResponse
    {
        if (! $badge->topic_id || $badge->topic->user_id !== auth()->id()) {
            abort(404);
        }
        $topics = Topic::query()
            ->where('user_id', auth()->id())
            ->orderBy('order')
            ->orderBy('title')
            ->get();

        return view('teacher.badges.edit', compact('badge', 'topics'));
    }

    public function update(UpdateBadgeRequest $request, Badge $badge): RedirectResponse
    {
        $badge->update([
            'topic_id' => $request->input('topic_id'),
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'icon' => $request->input('icon') ?: '🏆',
            'award_for' => $request->input('award_for'),
        ]);

        return redirect()->route('teacher.badges.index')->with('status', 'Badge updated.');
    }

    public function destroy(Badge $badge): RedirectResponse
    {
        if (! $badge->topic_id || $badge->topic->user_id !== auth()->id()) {
            abort(404);
        }
        $badge->delete();

        return redirect()->route('teacher.badges.index')->with('status', 'Badge deleted.');
    }
}
