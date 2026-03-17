<?php

declare(strict_types=1);

namespace App\Http\Requests\Grade;

use App\Domain\Submission\Models\Submission;
use Illuminate\Foundation\Http\FormRequest;

class GradeSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        /** @var Submission $submission */
        $submission = $this->route('submission');

        return [
            'score' => ['required', 'numeric', 'min:0', 'max:'.(float) $submission->assignment->max_score],
            'feedback' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
