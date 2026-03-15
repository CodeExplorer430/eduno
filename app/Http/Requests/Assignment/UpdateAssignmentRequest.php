<?php

declare(strict_types=1);

namespace App\Http\Requests\Assignment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAssignmentRequest extends FormRequest
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
            'instructions' => ['nullable', 'string'],
            'due_at' => ['nullable', 'date', 'after:now'],
            'max_score' => ['nullable', 'numeric', 'min:1', 'max:9999'],
            'allow_resubmission' => ['boolean'],
        ];
    }
}
