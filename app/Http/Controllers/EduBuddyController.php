<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EduBuddyController extends Controller
{
    private const SYSTEM_INSTRUCTION = <<<'TEXT'
You are EduBuddy, a friendly and kind learning assistant for students in grades 4 and 5.

Rules:
- Use simple words and short sentences. Imagine you're talking to a 9–11 year old.
- Be encouraging, warm, and patient. Use phrases like "Great question!", "You're thinking like a scientist!"
- Focus on science and environment topics: living vs non-living things, the water cycle, animals and habitats, plants, recycling, and nature.
- When students ask for quiz help, give hints and explanations—never give the direct answer. Help them figure it out.
- Keep replies fairly short: usually 2 to 5 sentences so it's easy to read.
- If a question is off-topic or you're unsure, gently steer back to learning or say you're here to help with their environment and science topics.
- Use the occasional emoji if it fits (e.g. 🌱 💧 🐻) but don't overdo it.
TEXT;

    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $apiKey = Str::of(config('services.gemini.key', ''))->trim();
        if ($apiKey->isEmpty()) {
            return response()->json([
                'reply' => "I'm still getting set up! Please ask your teacher to add the EduBuddy API key. I'll be here to help soon! 🌱",
            ]);
        }

        $message = Str::of($request->input('message'))->trim();
        if ($message->isEmpty()) {
            return response()->json(['reply' => 'Type something and I\'ll try to help! 😊']);
        }

        $models = ['gemini-2.0-flash', 'gemini-2.5-flash', 'gemini-2.5-pro', 'gemini-1.5-flash', 'gemini-1.5-pro', 'gemini-pro'];

        $lastError = null;
        foreach ($models as $model) {
            $useSystemInstruction = str_starts_with($model, 'gemini-1.5') || str_starts_with($model, 'gemini-2');
            $payload = [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [['text' => $message->toString()]],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 512,
                ],
            ];
            if ($useSystemInstruction) {
                $payload['systemInstruction'] = [
                    'parts' => [['text' => self::SYSTEM_INSTRUCTION]],
                ];
            } else {
                $payload['contents'][0]['parts'][0]['text'] = self::SYSTEM_INSTRUCTION."\n\nStudent asks: ".$message->toString();
            }

            foreach (['v1beta', 'v1'] as $apiVersion) {
                $url = "https://generativelanguage.googleapis.com/{$apiVersion}/models/{$model}:generateContent";
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                ])->timeout(30)->post($url.'?key='.$apiKey->toString(), $payload);

                if (! $response->successful()) {
                    $body = $response->json();
                    $lastError = $body['error']['message'] ?? $response->reason();
                    Log::warning('EduBuddy Gemini API error', [
                        'model' => $model,
                        'version' => $apiVersion,
                        'status' => $response->status(),
                        'body' => $body,
                    ]);

                    continue;
                }

                $data = $response->json();
                $text = $this->extractText($data);

                if ($text !== null && $text !== '') {
                    return response()->json(['reply' => trim($text)]);
                }
            }

        }

        Log::warning('EduBuddy: No text from Gemini after trying all models', ['last_error' => $lastError]);

        return response()->json([
            'reply' => 'Sorry, I had a little hiccup. Please try again in a moment! If it keeps happening, tell your teacher. 😊',
        ]);
    }

    private function extractText(array $data): ?string
    {
        $candidates = $data['candidates'] ?? null;
        if (! is_array($candidates) || empty($candidates)) {
            return null;
        }

        $first = $candidates[0];
        $content = $first['content'] ?? null;
        if (! is_array($content)) {
            return null;
        }

        $parts = $content['parts'] ?? [];
        if (! is_array($parts) || empty($parts)) {
            return null;
        }

        return $parts[0]['text'] ?? null;
    }
}
