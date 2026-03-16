<?php

declare(strict_types=1);

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;

class CreateCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:20', 'unique:courses'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'department' => ['required', 'string', 'max:100'],
            'term' => ['required', 'string', 'max:50'],
            'academic_year' => ['required', 'string', 'max:20'],
        ];
    }
}
