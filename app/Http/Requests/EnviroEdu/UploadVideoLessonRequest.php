<?php

namespace App\Http\Requests\EnviroEdu;

use Illuminate\Foundation\Http\FormRequest;

class UploadVideoLessonRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->role === 'teacher';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'grade_level' => ['required', 'string', 'in:grade4,grade5'],
            'video' => ['nullable', 'file', 'mimes:mp4,webm,ogg', 'max:102400'],
            'key_points' => ['nullable', 'array'],
            'key_points.*' => ['string', 'max:500'],
        ];
    }
}
