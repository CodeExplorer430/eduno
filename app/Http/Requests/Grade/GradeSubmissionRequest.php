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
        /** @var Submission|null $submission */
        $submission = $this->route('submission');
        $maxScore   = $submission?->assignment->max_score ?? PHP_INT_MAX;

        return [
            'score'    => ['required', 'numeric', 'min:0', 'max:'.$maxScore],
            'feedback' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
