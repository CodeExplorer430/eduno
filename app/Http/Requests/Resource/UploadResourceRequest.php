<?php

declare(strict_types=1);

namespace App\Http\Requests\Resource;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UploadResourceRequest extends FormRequest
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
            'file' => [
                'required',
                'file',
                'mimes:pdf,docx,pptx,xlsx,mp4,zip',
                'max:51200',
            ],
            'visibility' => ['required', Rule::in(['enrolled', 'public'])],
        ];
    }
}
