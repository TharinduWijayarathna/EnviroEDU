<?php

namespace App\Http\Requests;

use App\Enums\BadgeAwardFor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBadgeRequest extends FormRequest
{
    public function authorize(): bool
    {
        $badge = $this->route('badge');
        if (! $badge || ! $badge->topic_id) {
            return false;
        }

        return $badge->topic->user_id === $this->user()?->id;
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
            'image_path' => ['nullable', 'string', 'max:500'],
            'badge_image' => ['nullable', 'image', 'mimes:jpeg,png,gif,webp', 'max:2048'],
            'award_for' => ['required', 'string', Rule::enum(BadgeAwardFor::class)],
        ];
    }
}
