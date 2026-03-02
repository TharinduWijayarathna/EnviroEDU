<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GenerateQuizRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('teacher') ?? false;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'prompt' => ['required', 'string', 'min:15', 'max:2000'],
            'topic_id' => ['nullable', 'integer', 'exists:topics,id'],
            'grade_level' => ['nullable', 'integer', Rule::in(config('app.grade_levels', [4, 5]))],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'prompt.required' => 'Please describe the environmental topic for your quiz.',
            'prompt.min' => 'Please provide more detail (e.g. "Recycling for grade 4" or "Ocean ecosystems").',
        ];
    }
}
