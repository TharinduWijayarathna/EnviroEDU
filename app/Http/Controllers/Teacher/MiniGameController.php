<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\GenerateMiniGameRequest;
use App\Http\Requests\StoreMiniGameRequest;
use App\Http\Requests\UpdateMiniGameRequest;
use App\Models\GameTemplate;
use App\Models\MiniGame;
use App\Models\Topic;
use App\Services\MiniGameGeneratorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $topics = Topic::query()->where('user_id', auth()->id())->orderBy('order')->orderBy('title')->get();

        return view('teacher.mini-games.create', compact('topics'));
    }

    public function generate(GenerateMiniGameRequest $request, MiniGameGeneratorService $generator): RedirectResponse
    {
        $gradeLevel = $request->input('grade_level') ? (int) $request->input('grade_level') : null;
        $result = $generator->generate(
            $request->input('prompt'),
            $request->input('game_type'),
            $gradeLevel
        );

        if (isset($result['error'])) {
            return back()->withErrors(['prompt' => $result['error']])->withInput();
        }

        $template = GameTemplate::query()->where('slug', 'environment_3d')->first();
        if (! $template) {
            return back()->withErrors(['prompt' => 'Environment 3D game template is not set up. Run: php artisan db:seed --class=GameTemplateSeeder'])->withInput();
        }

        $miniGame = MiniGame::query()->create([
            'user_id' => auth()->id(),
            'topic_id' => $request->input('topic_id') ?: null,
            'game_template_id' => $template->id,
            'title' => $result['title'],
            'description' => $result['description'] ?? null,
            'prompt' => $request->input('prompt'),
            'config' => $result['config'],
            'grade_level' => $gradeLevel,
            'is_published' => false,
        ]);

        return redirect()->route('teacher.mini-games.show', $miniGame)->with('status', 'Game generated. You can publish it or edit the title.');
    }

    public function store(StoreMiniGameRequest $request): RedirectResponse
    {
        $template = GameTemplate::query()->findOrFail($request->input('game_template_id'));
        $config = $this->buildConfigFromRequest($request, $template->slug);

        MiniGame::query()->create([
            'user_id' => auth()->id(),
            'topic_id' => $request->input('topic_id') ?: null,
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
        $topics = Topic::query()->where('user_id', auth()->id())->orderBy('order')->orderBy('title')->get();

        return view('teacher.mini-games.edit', compact('miniGame', 'topics'));
    }

    public function update(UpdateMiniGameRequest $request, MiniGame $miniGame): RedirectResponse
    {
        if ($miniGame->user_id !== auth()->id()) {
            abort(403);
        }

        $templateSlug = $miniGame->gameTemplate->slug;
        $update = [
            'topic_id' => $request->input('topic_id') ?: null,
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'grade_level' => $request->input('grade_level') ? (int) $request->input('grade_level') : null,
            'is_published' => $request->boolean('is_published'),
        ];

        if ($templateSlug !== 'environment_3d') {
            $update['config'] = $this->buildConfigFromRequest($request, $templateSlug);
        }

        $miniGame->update($update);

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
            foreach ($request->input('config_categories', []) as $i => $c) {
                if (empty($c['id'] ?? '') || empty($c['label'] ?? '')) {
                    continue;
                }
                $imagePath = $this->resolveImagePath($request, 'config_categories', (int) $i);
                $cat = ['id' => $c['id'], 'label' => $c['label']];
                if ($imagePath !== null) {
                    $cat['image'] = $imagePath;
                }
                $categories[] = $cat;
            }
            $items = [];
            foreach ($request->input('config_items', []) as $i => $item) {
                if (empty($item['label'] ?? '')) {
                    continue;
                }
                $catId = $item['category_id'] ?? '';
                if (is_numeric($catId) && isset($categories[(int) $catId])) {
                    $catId = $categories[(int) $catId]['id'];
                }
                if ($catId === '') {
                    continue;
                }
                $imagePath = $this->resolveImagePath($request, 'config_items', (int) $i);
                $entry = ['label' => $item['label'], 'category_id' => $catId];
                if ($imagePath !== null) {
                    $entry['image'] = $imagePath;
                }
                $items[] = $entry;
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

    private function resolveImagePath(Request $request, string $prefix, int $index): ?string
    {
        $path = $request->input("{$prefix}.{$index}.image_path");
        if (is_string($path) && str_starts_with($path, 'game-images/')) {
            return $path;
        }

        $file = $request->file("{$prefix}.{$index}.image");
        if ($file && $file->isValid()) {
            return $file->store('game-images', 'public');
        }

        return null;
    }
}
