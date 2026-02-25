<?php

namespace App\Http\Requests;

use App\Enums\BadgeAwardFor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBadgeRequest extends FormRequest
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
        $teacherTopicIds = \App\Models\Topic::query()
            ->where('user_id', $this->user()->id)
            ->pluck('id')
            ->all();

        return [
            'topic_id' => ['required', 'integer', Rule::in($teacherTopicIds)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'icon' => ['nullable', 'string', 'max:50'],
            'image_path' => ['nullable', 'string', 'max:500'],
            'award_for' => ['required', 'string', Rule::enum(BadgeAwardFor::class)],
        ];
    }
}
