<?php

declare(strict_types=1);

namespace App\Http\Requests\Module;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLessonRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'type' => ['required', 'string', Rule::in(['text', 'pdf', 'video', 'link'])],
            'order_no' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
