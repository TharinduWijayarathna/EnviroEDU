<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTopicRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'grade_level' => ['nullable', 'integer', Rule::in(config('app.grade_levels', [4, 5]))],
            'video_url' => ['nullable', 'string', 'url', 'max:500'],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['boolean'],
        ];
    }
}
