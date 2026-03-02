<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $role = $this->input('role');

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'confirmed', 'min:8'],
            'role' => ['required', 'string', 'in:admin,teacher,student,parent'],
        ];

        if ($role === 'admin') {
            $rules['school_name'] = ['required', 'string', 'max:255'];
            $rules['school_code'] = ['required', 'string', 'max:60', 'regex:/^[a-zA-Z0-9\-]+$/', 'unique:schools,slug'];
        }

        if (in_array($role, ['teacher', 'student'], true)) {
            $rules['school_code'] = ['required', 'string', 'exists:schools,slug'];
        }

        if ($role === 'student') {
            $rules['grade_level'] = ['required', 'integer', 'in:4,5'];
        }

        if ($role === 'parent') {
            $rules['child_email'] = [
                'nullable',
                'email',
                'exists:users,email',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (blank($value)) {
                        return;
                    }
                    $student = \App\Models\User::query()
                        ->where('email', $value)
                        ->where('role', \App\Enums\Role::Student)
                        ->first();
                    if (! $student) {
                        $fail(__('messages.auth.child_email_not_student'));
                    }
                },
            ];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'school_code.required' => 'Please enter your school code.',
            'school_code.exists' => 'No school found with this code. Check the code and try again.',
            'school_code.unique' => 'This school code is already in use. Choose another.',
            'school_code.regex' => 'School code may only contain letters, numbers, and hyphens.',
            'grade_level.required' => 'Please select your grade.',
            'grade_level.in' => 'Please select Grade 4 or Grade 5.',
        ];
    }
}
