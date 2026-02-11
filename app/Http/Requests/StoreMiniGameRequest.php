<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMiniGameRequest extends FormRequest
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
            'game_template_id' => ['required', 'integer', 'exists:game_templates,id'],
            'title' => ['required', 'string', 'max:255'],
            'topic_id' => ['nullable', 'integer', 'exists:topics,id'],
            'description' => ['nullable', 'string', 'max:1000'],
            'grade_level' => ['nullable', 'integer', Rule::in(config('app.grade_levels', [4, 5]))],
            'is_published' => ['boolean'],
            'config' => ['nullable'],
        ];
    }
}
