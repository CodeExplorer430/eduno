<?php

declare(strict_types=1);

namespace App\Http\Requests\Submission;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGradeRequest extends FormRequest
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
            'score' => ['required', 'numeric', 'min:0'],
            'feedback' => ['nullable', 'string'],
        ];
    }
}
