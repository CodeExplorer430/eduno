<?php

declare(strict_types=1);

namespace App\Http\Requests\Module;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreResourceRequest extends FormRequest
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
            'file' => [
                'required',
                'file',
                'max:51200',
                'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,mp4,mp3,png,jpg,jpeg,gif',
            ],
            'visibility' => ['required', 'string', Rule::in(['enrolled', 'instructor', 'public'])],
        ];
    }
}
