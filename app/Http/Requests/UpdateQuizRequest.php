<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateQuizRequest extends FormRequest
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
            'topic_id' => ['nullable', 'integer', 'exists:topics,id'],
            'description' => ['nullable', 'string', 'max:1000'],
            'grade_level' => ['nullable', 'integer', Rule::in(config('app.grade_levels', [4, 5]))],
            'is_published' => ['boolean'],
            'questions' => ['required', 'array', 'min:1'],
            'questions.*.id' => ['nullable', 'integer', 'exists:quiz_questions,id'],
            'questions.*.question_text' => ['required', 'string', 'max:1000'],
            'questions.*.order' => ['nullable', 'integer', 'min:0'],
            'questions.*.options' => ['required', 'array', 'min:2'],
            'questions.*.options.*.id' => ['nullable', 'integer', 'exists:quiz_question_options,id'],
            'questions.*.options.*.option_text' => ['required', 'string', 'max:500'],
            'questions.*.options.*.is_correct' => ['boolean'],
            'questions.*.options.*.order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
