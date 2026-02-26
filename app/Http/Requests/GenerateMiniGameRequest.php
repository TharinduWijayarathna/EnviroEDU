<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateMiniGameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'game_type' => ['required', 'string', 'in:drag_drop,matching'],
            'prompt' => ['required', 'string', 'min:15', 'max:2000'],
            'topic_id' => ['nullable', 'integer', 'exists:topics,id'],
            'grade_level' => ['nullable', 'integer', 'min:1', 'max:12'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'prompt.required' => 'Please describe the environmental topic for your game.',
            'prompt.min' => 'Please provide more detail (e.g. "Recycling for grade 4" or "Ocean ecosystems").',
        ];
    }
}
