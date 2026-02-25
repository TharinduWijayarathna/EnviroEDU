<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BadgeImageService
{
    /**
     * Generate a badge image from topic, name and description (no user prompt).
     * Builds an image prompt from the badge context and calls the API.
     *
     * @return array{path: string, url: string}|array{success: false, message: string}|null
     */
    public function generateFromBadgeContext(string $topicTitle, string $badgeName, ?string $badgeDescription = null): ?array
    {
        $prompt = $this->buildPromptFromContext($topicTitle, $badgeName, $badgeDescription);

        return $this->generateAndStore($prompt);
    }

    /**
     * Build an image generation prompt from badge topic, name and description.
     */
    public function buildPromptFromContext(string $topicTitle, string $badgeName, ?string $badgeDescription = null): string
    {
        $parts = [
            'A small, simple educational badge or icon, square, suitable for a child-friendly app.',
            'Topic: '.$topicTitle.'.',
            'Badge name: '.$badgeName.'.',
        ];
        if (! empty($badgeDescription)) {
            $parts[] = 'Meaning: '.trim($badgeDescription);
        }
        $parts[] = 'Style: friendly, clean, colourful, no text in the image. Single recognisable symbol or scene.';

        return implode(' ', $parts);
    }

    /**
     * Generate a badge image using Gemini API (generateContent with image output) and store it.
     * Uses GEMINI_API_KEY from .env (Google AI Studio: https://aistudio.google.com/app/apikey).
     *
     * @return array{path: string, url: string}|array{success: false, message: string}|null Returns path/url on success, or array with message on known error, or null.
     */
    public function generateAndStore(string $prompt): ?array
    {
        $apiKey = config('services.gemini.key');
        if (empty($apiKey)) {
            Log::warning('BadgeImageService: GEMINI_API_KEY not set in .env');

            return ['success' => false, 'message' => 'GEMINI_API_KEY is not set in your .env file.'];
        }

        $result = $this->callGeminiImageApi($apiKey, $prompt);

        if (isset($result['success']) && $result['success'] === false) {
            return $result;
        }

        $base64Image = $result;
        if ($base64Image === null || $base64Image === '') {
            return ['success' => false, 'message' => 'Image generation failed. The API did not return an image. Try a different prompt or check your API key.'];
        }

        $binary = base64_decode($base64Image, true);
        if ($binary === false || strlen($binary) < 100) {
            Log::warning('BadgeImageService: Invalid base64 image data from API');

            return ['success' => false, 'message' => 'Invalid image data from API. Try again.'];
        }

        $filename = 'badges/'.Str::ulid().'.png';
        Storage::disk('public')->put($filename, $binary);

        return [
            'path' => $filename,
            'url' => asset('storage/'.$filename),
        ];
    }

    /**
     * Call Gemini API generateContent with image generation (works with Google AI Studio API key).
     * Tries image-generation model first, then gemini-2.0-flash-exp.
     *
     * @return string|null base64 image data, or array{success: false, message: string} on API error, or null.
     */
    private function callGeminiImageApi(string $apiKey, string $prompt): string|array|null
    {
        $models = [
            'gemini-2.0-flash-exp-image-generation',
            'gemini-2.0-flash-exp',
        ];
        $payload = $this->buildImageRequestPayload($prompt);
        $lastError = null;

        foreach ($models as $model) {
            $url = 'https://generativelanguage.googleapis.com/v1beta/models/'.$model.':generateContent?key='.$apiKey;
            $result = $this->postGenerateContent($url, $payload);
            if (is_string($result)) {
                return $result;
            }
            if (is_array($result) && isset($result['success']) && $result['success'] === false) {
                $lastError = $result;
                if (str_contains($result['message'] ?? '', '404') || str_contains($result['message'] ?? '', 'not found')) {
                    continue;
                }

                return $result;
            }
        }

        return $lastError ?? null;
    }

    private function buildImageRequestPayload(string $prompt): array
    {
        return [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                    ],
                ],
            ],
            'generationConfig' => [
                'responseModalities' => ['TEXT', 'IMAGE'],
                'responseMimeType' => 'text/plain',
            ],
        ];
    }

    private function postGenerateContent(string $url, array $payload): string|array|null
    {

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->timeout(90)->post($url, $payload);

            $status = $response->status();
            $body = $response->body();
            $data = $response->json();

            if (! $response->successful()) {
                $message = $this->extractErrorMessage($data, $body, $status);
                Log::warning('BadgeImageService: Gemini API error', [
                    'status' => $status,
                    'body' => strlen($body) > 800 ? substr($body, 0, 800).'...' : $body,
                ]);

                return ['success' => false, 'message' => $message];
            }

            $candidates = $data['candidates'] ?? [];
            $first = $candidates[0] ?? null;
            if (! $first) {
                Log::warning('BadgeImageService: No candidates in response', ['body' => $body]);

                return null;
            }

            $parts = $first['content']['parts'] ?? [];
            foreach ($parts as $part) {
                if (isset($part['inlineData']['data'])) {
                    return $part['inlineData']['data'];
                }
            }

            Log::warning('BadgeImageService: No image in response', ['parts_keys' => array_map(fn ($p) => array_keys($p), $parts)]);

            return null;
        } catch (RequestException $e) {
            Log::warning('BadgeImageService: Request failed', ['message' => $e->getMessage()]);

            return ['success' => false, 'message' => 'Request failed: '.$e->getMessage()];
        }
    }

    private function extractErrorMessage(?array $data, string $body, int $status): string
    {
        $message = $data['error']['message'] ?? null;
        if (is_string($message)) {
            return $message;
        }
        if (isset($data['error']['status'])) {
            return ($data['error']['status'] ?? 'Error').': '.($data['error']['message'] ?? $body);
        }
        if ($status === 400) {
            return 'Bad request (400). Your API key may be valid but this model or image generation might not be available. Check Google AI Studio for available models.';
        }
        if ($status === 403) {
            return 'Access denied (403). Check that GEMINI_API_KEY is correct and that image generation is enabled for your project.';
        }
        if ($status === 404) {
            return 'Model not found (404). The image generation model may have a different name in your region.';
        }
        if ($status === 429) {
            return 'Rate limit exceeded (429). Try again in a moment.';
        }

        return 'API error ('.$status.'). '.substr($body, 0, 200);
    }
}
