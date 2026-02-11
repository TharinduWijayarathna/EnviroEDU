<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMiniGameRequest;
use App\Http\Requests\UpdateMiniGameRequest;
use App\Models\GameTemplate;
use App\Models\MiniGame;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MiniGameController extends Controller
{
    public function index(): View
    {
        $miniGames = MiniGame::query()
            ->where('user_id', auth()->id())
            ->with('gameTemplate')
            ->latest()
            ->paginate(15);

        return view('teacher.mini-games.index', compact('miniGames'));
    }

    public function create(): View
    {
        $templates = GameTemplate::query()->orderBy('name')->get();

        return view('teacher.mini-games.create', compact('templates'));
    }

    public function store(StoreMiniGameRequest $request): RedirectResponse
    {
        $template = GameTemplate::query()->findOrFail($request->input('game_template_id'));
        $config = $this->buildConfigFromRequest($request, $template->slug);

        MiniGame::query()->create([
            'user_id' => auth()->id(),
            'game_template_id' => $request->input('game_template_id'),
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'config' => $config,
            'grade_level' => $request->input('grade_level') ? (int) $request->input('grade_level') : null,
            'is_published' => $request->boolean('is_published'),
        ]);

        return redirect()->route('teacher.mini-games.index')->with('status', 'Mini game created.');
    }

    public function show(MiniGame $miniGame): View|RedirectResponse
    {
        if ($miniGame->user_id !== auth()->id()) {
            abort(403);
        }
        $miniGame->load('gameTemplate');

        return view('teacher.mini-games.show', compact('miniGame'));
    }

    public function edit(MiniGame $miniGame): View|RedirectResponse
    {
        if ($miniGame->user_id !== auth()->id()) {
            abort(403);
        }
        $miniGame->load('gameTemplate');

        return view('teacher.mini-games.edit', compact('miniGame'));
    }

    public function update(UpdateMiniGameRequest $request, MiniGame $miniGame): RedirectResponse
    {
        if ($miniGame->user_id !== auth()->id()) {
            abort(403);
        }

        $config = $this->buildConfigFromRequest($request, $miniGame->gameTemplate->slug);

        $miniGame->update([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'config' => $config,
            'grade_level' => $request->input('grade_level') ? (int) $request->input('grade_level') : null,
            'is_published' => $request->boolean('is_published'),
        ]);

        return redirect()->route('teacher.mini-games.index')->with('status', 'Mini game updated.');
    }

    public function destroy(MiniGame $miniGame): RedirectResponse
    {
        if ($miniGame->user_id !== auth()->id()) {
            abort(403);
        }
        $miniGame->delete();

        return redirect()->route('teacher.mini-games.index')->with('status', 'Mini game deleted.');
    }

    /**
     * @param  array<string, mixed>  $request
     * @return array<string, mixed>
     */
    private function buildConfigFromRequest(\Illuminate\Http\Request $request, string $templateSlug): array
    {
        $config = $request->input('config');
        if (is_array($config)) {
            return $config;
        }
        if (is_string($config)) {
            $decoded = json_decode($config, true);

            return is_array($decoded) ? $decoded : [];
        }

        if ($templateSlug === 'drag_drop') {
            $categories = [];
            foreach ($request->input('config_categories', []) as $c) {
                if (! empty($c['id'] ?? '') && ! empty($c['label'] ?? '')) {
                    $categories[] = ['id' => $c['id'], 'label' => $c['label']];
                }
            }
            $items = [];
            foreach ($request->input('config_items', []) as $item) {
                if (empty($item['label'] ?? '')) {
                    continue;
                }
                $catId = $item['category_id'] ?? '';
                if (is_numeric($catId) && isset($categories[(int) $catId])) {
                    $catId = $categories[(int) $catId]['id'];
                }
                if ($catId !== '') {
                    $items[] = ['label' => $item['label'], 'category_id' => $catId];
                }
            }

            return ['categories' => $categories, 'items' => $items];
        }

        if ($templateSlug === 'multiple_choice') {
            $questions = [];
            foreach ($request->input('config_questions', []) as $q) {
                if (empty($q['question_text'] ?? '')) {
                    continue;
                }
                $correctIndex = (int) ($q['correct'] ?? 0);
                $options = [];
                foreach ($q['options'] ?? [] as $i => $opt) {
                    if (! empty($opt['text'] ?? '')) {
                        $options[] = ['text' => $opt['text'], 'is_correct' => $i === $correctIndex];
                    }
                }
                if (count($options) >= 2) {
                    $questions[] = ['question_text' => $q['question_text'], 'options' => $options];
                }
            }

            return ['questions' => $questions];
        }

        if ($templateSlug === 'matching') {
            $pairs = [];
            foreach ($request->input('config_pairs', []) as $p) {
                if (! empty($p['left'] ?? '') && ! empty($p['right'] ?? '')) {
                    $pairs[] = ['left' => $p['left'], 'right' => $p['right']];
                }
            }

            return ['pairs' => $pairs];
        }

        return [];
    }
}
