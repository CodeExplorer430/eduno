<?php

declare(strict_types=1);

namespace App\Http\Requests\Submission;

use Illuminate\Foundation\Http\FormRequest;

class UploadSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'files' => ['required', 'array', 'min:1', 'max:5'],
            'files.*' => ['required', 'file', 'mimes:pdf,docx,pptx,zip,txt', 'max:10240'],
        ];
    }
}
