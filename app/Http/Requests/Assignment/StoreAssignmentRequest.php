<?php

declare(strict_types=1);

namespace App\Http\Requests\Assignment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAssignmentRequest extends FormRequest
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
        $knownMimes = [
            'application/pdf', 'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/zip', 'text/plain', 'image/png', 'image/jpeg',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        return [
            'title' => ['required', 'string', 'max:255'],
            'instructions' => ['nullable', 'string'],
            'due_at' => ['nullable', 'date', 'after:now'],
            'max_score' => ['nullable', 'numeric', 'min:1', 'max:9999'],
            'allow_resubmission' => ['boolean'],
            'allowed_file_types' => ['nullable', 'array'],
            'allowed_file_types.*' => ['string', Rule::in($knownMimes)],
        ];
    }
}
