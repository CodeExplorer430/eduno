<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseSectionRequest extends FormRequest
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
            'section_name' => ['required', 'string', 'max:255'],
            'schedule_text' => ['nullable', 'string', 'max:255'],
        ];
    }
}
