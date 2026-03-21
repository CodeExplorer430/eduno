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
        $maxScore = $this->route('grade')->submission->assignment->max_score;

        return [
            'score' => ['required', 'numeric', 'min:0', 'max:'.$maxScore],
            'feedback' => ['nullable', 'string'],
        ];
    }
}
