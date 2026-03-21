<?php

declare(strict_types=1);

namespace App\Http\Requests\Lesson;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateLessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['text', 'video', 'document', 'quiz'])],
            'content' => ['nullable', 'string'],
            'order_no' => ['required', 'integer', 'min:0'],
            'published' => ['boolean'],
        ];
    }
}
