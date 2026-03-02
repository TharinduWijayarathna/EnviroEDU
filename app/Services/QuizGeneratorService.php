<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class QuizGeneratorService
{
    private const SYSTEM_PROMPT = <<<'TEXT'
You are an educational quiz generator for EnviroEdu, an environmental learning platform for kids (e.g. grade 5 and below).
Generate ONLY content about environmental science, sustainability, nature, recycling, climate, ecosystems, conservation, habitats, or similar topics.
Create a multiple-choice quiz with clear questions and exactly one correct answer per question.
Output valid JSON only, no markdown, with this exact structure:
{"title":"Short quiz title","description":"One sentence description","questions":[{"question_text":"Question text?","options":[{"option_text":"Correct answer","is_correct":true},{"option_text":"Wrong answer","is_correct":false},{"option_text":"Another wrong answer","is_correct":false},{"option_text":"Another wrong answer","is_correct":false}]}]}
Rules: Generate 4 to 8 questions. Each question must have exactly 4 options. Exactly one option per question must have is_correct: true. All content must be environment-themed and age-appropriate. Use clear, simple language.
TEXT;

    /**
     * Generate quiz content (title, description, questions with options) from a teacher's prompt.
     *
     * @return array{title: string, description: string, questions: array}|array{error: string}
     */
    public function generate(string $teacherPrompt, ?int $gradeLevel = null): array
    {
        $apiKey = config('services.gemini.key');
        if (empty($apiKey)) {
            Log::warning('QuizGeneratorService: GEMINI_API_KEY not set');

            return ['error' => 'GEMINI_API_KEY is not set in your .env file.'];
        }

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
                'maxOutputTokens' => 4096,
                'responseMimeType' => 'application/json',
            ],
            'systemInstruction' => [
                'parts' => [
                    ['text' => self::SYSTEM_PROMPT],
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
                        Log::warning('QuizGeneratorService: Gemini API error', [
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
                        Log::warning('QuizGeneratorService: Invalid JSON from Gemini', ['text' => substr($text, 0, 500)]);
                        $lastError = 'AI returned invalid format. Try again.';

                        continue;
                    }

                    return $this->normalizeGeneratedQuiz($decoded);
                } catch (RequestException $e) {
                    $lastError = 'Request failed: '.$e->getMessage();
                    Log::warning('QuizGeneratorService: Request failed', ['message' => $e->getMessage()]);
                }
            }
        }

        return ['error' => $lastError ?? 'AI generation failed. Try again.'];
    }

    /**
     * @param  array<string, mixed>  $decoded
     * @return array{title: string, description: string, questions: array}
     */
    private function normalizeGeneratedQuiz(array $decoded): array
    {
        $title = isset($decoded['title']) && is_string($decoded['title'])
            ? substr(trim($decoded['title']), 0, 255)
            : 'Environmental Quiz';
        $description = isset($decoded['description']) && is_string($decoded['description'])
            ? trim($decoded['description'])
            : '';

        $questions = [];
        foreach ($decoded['questions'] ?? [] as $index => $q) {
            if (! is_array($q) || empty($q['question_text'])) {
                continue;
            }
            $options = [];
            $hasCorrect = false;
            foreach ($q['options'] ?? [] as $opt) {
                if (! is_array($opt) || empty($opt['option_text'])) {
                    continue;
                }
                $isCorrect = (bool) ($opt['is_correct'] ?? false);
                if ($isCorrect) {
                    $hasCorrect = true;
                }
                $options[] = [
                    'option_text' => trim((string) $opt['option_text']),
                    'is_correct' => $isCorrect,
                ];
            }
            if (count($options) < 2) {
                continue;
            }
            if (! $hasCorrect) {
                $options[0]['is_correct'] = true;
            }
            $questions[] = [
                'question_text' => trim((string) $q['question_text']),
                'order' => $index,
                'options' => $options,
            ];
        }

        if (count($questions) < 1) {
            $questions = [
                [
                    'question_text' => 'What is recycling?',
                    'order' => 0,
                    'options' => [
                        ['option_text' => 'Turning waste into new materials', 'is_correct' => true],
                        ['option_text' => 'Throwing trash away', 'is_correct' => false],
                        ['option_text' => 'Burning garbage', 'is_correct' => false],
                        ['option_text' => 'Burying waste', 'is_correct' => false],
                    ],
                ],
            ];
        }

        return [
            'title' => $title,
            'description' => $description,
            'questions' => $questions,
        ];
    }
}
