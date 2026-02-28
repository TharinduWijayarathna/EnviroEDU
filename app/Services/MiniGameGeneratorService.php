<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MiniGameGeneratorService
{
    private const SYSTEM_DRAG_DROP = <<<'TEXT'
You are an educational game generator for EnviroEdu, an environmental learning platform for kids (e.g. grade 5 and below).
Generate ONLY content about environmental science, sustainability, nature, recycling, climate, ecosystems, conservation, habitats, or similar topics.
Create a SORTING / DRAG-AND-DROP game: students will drag items into the correct category.
Output valid JSON only, no markdown, with this exact structure:
{"title":"Short game title","description":"One sentence description","game_type":"drag_drop","categories":[{"id":"unique_id","label":"emoji Category name"}],"items":[{"label":"emoji Item name or short phrase","category_id":"unique_id"}]}
Rules: Use 2 or 3 categories. Use 4 to 8 items total. Each item must have category_id matching one category id. All content must be environment-themed and age-appropriate.
IMPORTANT: Every label (category labels and item labels) MUST start with one relevant emoji so kids get a clear visual cue. Use a DIFFERENT emoji per label (e.g. 🍂 Compost, ♻️ Recycling, 🌱 Seedling) so kids can tell them apart—not the same icon for everything. Use emojis like: 🌳 🍃 🌍 ♻️ 🗑️ 🐝 🦋 🌱 💧 ☀️ 🌊 🐠 🏔️ 🌲 🍂 🐻 🐸 🦎 🌻 🥕 🍎 🧴 📦 ⚡ 🌈
TEXT;

    private const SYSTEM_MATCHING = <<<'TEXT'
You are an educational game generator for EnviroEdu, an environmental learning platform for kids (e.g. grade 5 and below).
Generate ONLY content about environmental science, sustainability, nature, recycling, climate, ecosystems, conservation, habitats, or similar topics.
Create a MATCHING PAIRS game: students will match each left item with the correct right item.
Output valid JSON only, no markdown, with this exact structure:
{"title":"Short game title","description":"One sentence description","game_type":"matching","pairs":[{"left":"emoji Term or concept","right":"emoji Definition or match"}]}
Rules: Generate 4 to 6 pairs. Left can be a word, phrase, or concept; right is its definition, cause, effect, or match. All content must be environment-themed and age-appropriate.
IMPORTANT: Every "left" and "right" value MUST start with one relevant emoji so kids get a clear visual cue. Use a DIFFERENT emoji per pair (e.g. 🍂 Compost / 🌱 Decayed matter..., ♻️ Recycling / 🔄 Turning waste...) so kids can tell them apart—not the same icon for every item. Use emojis like: 🌳 🍃 🌍 ♻️ 🗑️ 🐝 🦋 🌱 💧 ☀️ 🌊 🐠 🏔️ 🌲 🍂 🐻 🐸 🦎 🌻 🥕 🍎 🧴 📦 ⚡ 🌈
TEXT;

    /**
     * Generate game content (drag_drop or matching) from a teacher's prompt. Environment-only.
     *
     * @return array{title: string, description: string, config: array}|array{error: string}
     */
    public function generate(string $teacherPrompt, string $gameType, ?int $gradeLevel = null): array
    {
        $apiKey = config('services.gemini.key');
        if (empty($apiKey)) {
            Log::warning('MiniGameGeneratorService: GEMINI_API_KEY not set');

            return ['error' => 'GEMINI_API_KEY is not set in your .env file.'];
        }

        $systemPrompt = $gameType === 'matching' ? self::SYSTEM_MATCHING : self::SYSTEM_DRAG_DROP;
        $userPrompt = $teacherPrompt;
        if ($gradeLevel !== null) {
            $userPrompt .= "\nTarget grade level: {$gradeLevel}. Keep language and concepts appropriate for this age.";
        }

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $userPrompt],
                    ],
                ],
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 2048,
                'responseMimeType' => 'application/json',
            ],
            'systemInstruction' => [
                'parts' => [
                    ['text' => $systemPrompt],
                ],
            ],
        ];

        $models = ['gemini-2.5-flash', 'gemini-2.5-pro', 'gemini-2.0-flash'];
        $lastError = null;

        foreach (['v1', 'v1beta'] as $apiVersion) {
            foreach ($models as $model) {
                $url = 'https://generativelanguage.googleapis.com/'.$apiVersion.'/models/'.$model.':generateContent?key='.$apiKey;

                try {
                    $response = Http::withHeaders([
                        'Content-Type' => 'application/json',
                    ])->timeout(60)->post($url, $payload);

                    if (! $response->successful()) {
                        $data = $response->json();
                        $lastError = $data['error']['message'] ?? 'API error '.$response->status();
                        Log::warning('MiniGameGeneratorService: Gemini API error', [
                            'model' => $model,
                            'version' => $apiVersion,
                            'status' => $response->status(),
                            'body' => $response->body(),
                        ]);

                        continue;
                    }

                    $data = $response->json();
                    $candidates = $data['candidates'] ?? [];
                    $first = $candidates[0] ?? null;
                    if (! $first) {
                        $lastError = 'No response from AI. Try again.';

                        continue;
                    }

                    $parts = $first['content']['parts'] ?? [];
                    $text = $parts[0]['text'] ?? '';
                    if ($text === '') {
                        $lastError = 'Empty response from AI. Try a different prompt.';

                        continue;
                    }

                    $decoded = json_decode(trim($text), true);
                    if (! is_array($decoded)) {
                        Log::warning('MiniGameGeneratorService: Invalid JSON from Gemini', ['text' => substr($text, 0, 500)]);
                        $lastError = 'AI returned invalid format. Try again.';

                        continue;
                    }

                    return $this->normalizeGeneratedGame($decoded, $gameType);
                } catch (RequestException $e) {
                    $lastError = 'Request failed: '.$e->getMessage();
                    Log::warning('MiniGameGeneratorService: Request failed', ['message' => $e->getMessage()]);
                }
            }
        }

        return ['error' => $lastError ?? 'AI generation failed. Try again.'];
    }

    /**
     * @param  array<string, mixed>  $decoded
     * @return array{title: string, description: string, config: array}
     */
    private function normalizeGeneratedGame(array $decoded, string $gameType): array
    {
        $title = isset($decoded['title']) && is_string($decoded['title'])
            ? substr(trim($decoded['title']), 0, 255)
            : 'Environmental Game';
        $description = isset($decoded['description']) && is_string($decoded['description'])
            ? trim($decoded['description'])
            : '';

        if ($gameType === 'matching') {
            $pairs = [];
            foreach ($decoded['pairs'] ?? [] as $p) {
                if (! is_array($p) || empty($p['left']) || empty($p['right'])) {
                    continue;
                }
                $pairs[] = [
                    'left' => trim((string) $p['left']),
                    'right' => trim((string) $p['right']),
                ];
            }
            if (count($pairs) < 2) {
                $pairs = [
                    ['left' => 'Recycling', 'right' => 'Turning waste into new materials'],
                    ['left' => 'Compost', 'right' => 'Decayed organic matter for soil'],
                ];
            }

            return [
                'title' => $title,
                'description' => $description,
                'config' => [
                    'game_type' => 'matching',
                    'pairs' => $pairs,
                ],
            ];
        }

        $categories = [];
        foreach ($decoded['categories'] ?? [] as $c) {
            if (! is_array($c) || empty($c['id']) || empty($c['label'])) {
                continue;
            }
            $categories[] = [
                'id' => trim((string) $c['id']),
                'label' => trim((string) $c['label']),
            ];
        }
        $items = [];
        $categoryIds = array_column($categories, 'id');
        foreach ($decoded['items'] ?? [] as $item) {
            if (! is_array($item) || empty($item['label'])) {
                continue;
            }
            $catId = $item['category_id'] ?? $categoryIds[0] ?? '';
            if (! in_array($catId, $categoryIds, true)) {
                $catId = $categoryIds[0] ?? '';
            }
            $items[] = [
                'label' => trim((string) $item['label']),
                'category_id' => $catId,
            ];
        }
        if (count($categories) < 2) {
            $categories = [
                ['id' => 'recyclable', 'label' => 'Recyclable'],
                ['id' => 'not_recyclable', 'label' => 'Not recyclable'],
            ];
            $categoryIds = ['recyclable', 'not_recyclable'];
        }
        if (count($items) < 2) {
            $items = [
                ['label' => 'Plastic bottle', 'category_id' => $categoryIds[0]],
                ['label' => 'Banana peel', 'category_id' => $categoryIds[1] ?? $categoryIds[0]],
            ];
        }

        return [
            'title' => $title,
            'description' => $description,
            'config' => [
                'game_type' => 'drag_drop',
                'categories' => $categories,
                'items' => $items,
            ],
        ];
    }
}
