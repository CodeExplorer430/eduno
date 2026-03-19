<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed[]>
     */
    public function rules(): array
    {
        return [
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'section_name' => ['required', 'string', 'max:255'],
            'instructor_id' => ['required', 'integer', 'exists:users,id'],
            'schedule_text' => ['nullable', 'string', 'max:255'],
        ];
    }
}
