<?php

declare(strict_types=1);

namespace App\Http\Requests\Submission;

use App\Domain\Assignment\Models\Assignment;
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
        /** @var Assignment|null $assignment */
        $assignment = $this->route('assignment');

        if ($assignment instanceof Assignment && ! empty($assignment->allowed_file_types)) {
            $mimeRule = 'mimetypes:' . implode(',', $assignment->allowed_file_types);
        } else {
            $mimeRule = 'mimes:pdf,docx,pptx,zip,txt';
        }

        return [
            'files' => ['required', 'array', 'min:1', 'max:5'],
            'files.*' => ['required', 'file', $mimeRule, 'max:10240'],
        ];
    }
}
